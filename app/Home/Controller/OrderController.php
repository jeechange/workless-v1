<?php

namespace Home\Controller;

use Admin\DModel\PaymentWxDModel;
use Admin\DModel\ServiceDModel;
use Admin\DModel\ServiceOrderDModel;
use Admin\Entity\ServiceOrder;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 10:13
 */
class OrderController extends CommonController {


    public function myOrder() {

        $sid = $this->getUser("sid");

        if (!$sid) {
            $this->assign("lists", array());
            return $this->display();
        }

        $serviceOrderDM = ServiceOrderDModel::getInstance();

        $lists = $serviceOrderDM->name("o")->where("o.sid=$sid")->order("o.id", "DESC")->getArray();

        $this->assign("lists", $lists);

        return $this->display();
    }

    public function orderConfirm() {

        // return $this->display();
        $id = Q()->get->get("id");

        if (!$id) {
            $this->assign("message", "非法操作");
            return $this->display();
        }

        $serviceOrderDM = ServiceOrderDModel::getInstance();


        /** @var ServiceOrder $order */

        $order = $serviceOrderDM->find($id);

        if (!$order) {
            $this->assign("message", "订单信息获取失败");
            return $this->display();
        }

        if ($order->getStatus() != 0) {
            $this->assign("message", "只能对未付款订单操作");
            return $this->display();
        }

        $this->assign("order", $order);

        $serviceDM = ServiceDModel::getInstance();

        $service = $serviceDM->find($order->getServiceId());

        $this->assign("service", $service);

        $sCode = explode("_", $service->getSCode());

        $unit = "";
        switch ($sCode[0]) {
            case  "workless":
                $unit = "人";
                break;
            case  "sms":
                $unit = "条";
                break;
        }


        $this->assign("unit", $unit);
        $this->assign("typesMemo", $serviceOrderDM->typesMemo);

        $this->assign("id", $id);

        return $this->display();
    }

    public function payOrder() {


        //return $this->display();
        $id = Q()->get->get("id");

        if (!$id) {
            $this->assign("message", "非法操作");
            return $this->display();
        }

        $serviceOrderDM = ServiceOrderDModel::getInstance();


        /** @var ServiceOrder $order */

        $order = $serviceOrderDM->find($id);

        if (!$order) {
            $this->assign("message", "订单信息获取失败");
            return $this->display();
        }

        if ($order->getStatus() != 0) {
            $this->assign("message", "只能对未付款订单操作");
            return $this->display();
        }


        $this->assign("order", $order);

        $serviceDM = ServiceDModel::getInstance();

        $service = $serviceDM->find($order->getServiceId());

        $this->assign("service", $service);

        $sCode = explode("_", $service->getSCode());

        $unit = "";
        switch ($sCode[0]) {
            case  "workless":
                $unit = "人";
                break;
            case  "sms":
                $unit = "条";
                break;
        }


        include_once dirname(dirname(__DIR__)) . "/Admin/Service/WxPayPubHelper/WxPayPubHelper.php";

        $config = C("wechat");

        //将商家的app_id，mchid等写入微信配置文件
        \WxPayConf_pub::$APPID = $config["app_id"];
        \WxPayConf_pub::$MCHID = $config["mchid"];
        \WxPayConf_pub::$KEY = $config["mckey"];
        \WxPayConf_pub::$APPSECRET = $config["app_secret"];
        \WxPayConf_pub::$NOTIFY_URL = url("home_Notify_wxNotify", array(), true);


        //统一支付接口
        $unifiedOrder = new \UnifiedOrder_pub();
//        $unifiedOrder->setParameter("openid", $this->openid); //用户的openid
        $unifiedOrder->setParameter("body", "{$service->getNames()}（{$order->getNums()}{$unit}"); //商品描述

        //自定义订单号，此处仅作举例

        $money = $order->getMoney();

        $money = 0.01;

        $out_trade_no = sprintf("%s%s", date("YmdHis"), rand_string(8, 1));
        $unifiedOrder->setParameter("out_trade_no", $out_trade_no); //商户订单号
        $unifiedOrder->setParameter("total_fee", intval(($money) * 100)); //总金额
        $unifiedOrder->setParameter("notify_url", \WxPayConf_pub::$NOTIFY_URL); //通知地址
        $unifiedOrder->setParameter("trade_type", "NATIVE"); //交易类型(h5)
        $unifiedOrder->setParameter("time_expire", date("YmdHis", time() + 180)); //失效时间2min

        $result = $unifiedOrder->getResult();

        if (!$result || $result["return_code"] != "SUCCESS") {
            $this->assign("message", sprintf("微信服务器返回失败:%s", $result ? $result["return_msg"] : ""));
            return $this->display();
        }

        $code_url = $result["code_url"] ?: "weixin://wxpay/bizpayurl/up?pr=NwY5Mz9&groupid=00";


        $fileName = md5($code_url) . ".png";

        //生成二维码
        $errorCorrectionLevel = 'H'; //容错级别
        $matrixPointSize = 6; //生成图片大小

        include_once main()->getVendorRoot() . '/phpqr/phpqrcode.php';

        $baseRoot = dirname(Q()->server->get("SCRIPT_FILENAME"));

        $filesDir = "/public/wxPayPngRoot/" . date("Ym");
        $wxPayPngRoot = $baseRoot . $filesDir;
        if (!is_dir($wxPayPngRoot)) {
            mkdir($wxPayPngRoot, 0777, true);
        }
        \QRcode::png($code_url, $wxPayPngRoot . "/" . $fileName, $errorCorrectionLevel, $matrixPointSize, 2);

        $uri = trim(dirname(Q()->server->get("SCRIPT_NAME")), "/") . $filesDir . "/" . $fileName;

        $this->assign("uri", $uri);


        $order->setPayTime(nowTime());
        $serviceOrderDM->save($order)->flush($order);

        $paymentWxDM = PaymentWxDModel::getInstance();
        $paymentWx = $paymentWxDM->newEntity();

        $paymentWx->setTradeNo($out_trade_no);
        $paymentWx->setSid($order->getSid());
        $paymentWx->setUserId($order->getUserId());
        $paymentWx->setRelateTable("ServiceOrder");
        $paymentWx->setRelateId($order->getId());
        $paymentWx->setAddTime(nowTime());
        $paymentWx->setMoney($order->getMoney());
        $paymentWx->setStatus(0);


        $paymentWxDM->add($paymentWx)->flush($paymentWx);


        return $this->display();
    }
}
