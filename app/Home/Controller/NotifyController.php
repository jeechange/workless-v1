<?php

namespace Home\Controller;

use Admin\DModel\CompanyServiceDModel;
use Admin\DModel\PaymentWxDModel;
use Admin\DModel\ServiceDModel;
use Admin\DModel\ServiceOrderDModel;
use Admin\Entity\PaymentWx;
use Admin\Entity\Service;
use Admin\Entity\ServiceOrder;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/20
 * Time: 16:49
 */
class NotifyController extends CommonController {


    public function wxNotify() {

        libxml_disable_entity_loader(true);
        $xml = isset($GLOBALS['HTTP_RAW_POST_DATA']) && $GLOBALS['HTTP_RAW_POST_DATA'] ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");


        //loginfo("/notify_h5", "订单返回", array("xml" => $xml, "get" => Q()->get->all(), "post" => Q()->post->all()));

        $config = C("wechat");
        include_once dirname(dirname(__DIR__)) . "/Admin/Service/WxPayPubHelper/WxPayPubHelper.php";
        \WxPayConf_pub::$APPID = $config["app_id"];
        \WxPayConf_pub::$MCHID = $config["mchid"];
        \WxPayConf_pub::$KEY = $config["mckey"];
        \WxPayConf_pub::$APPSECRET = $config["app_secret"];
        \WxPayConf_pub::$NOTIFY_URL = url("home_Notify_wxNotify", array(), true);


        //使用通用通知接口
        $notify = new \Notify_pub();

        //存储微信的回调,并转换成关联数组，以方便数据处理

        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。

        //当微信返回的签名与本地签名不一致时
        if ($notify->checkSign() == FALSE) {
            $notify->setReturnParameter("return_code", "FAIL"); //设置返回微信的xml数据--失败状态码
            $notify->setReturnParameter("return_msg", "签名失败"); //设置返回微信的xml数据--返回信息

            $returnXml = $notify->returnXml();//将失败状态的xml数据返回微信，微信将重新发送通知
            return $this->getResponse($returnXml);
        }

        $notify->setReturnParameter("return_code", "FAIL"); //设置返回微信的xml数据--失败状态码
        $result = $notify->xmlToArray($xml);//将微信的返回xml转换成关联数组，以方便数据处理

        $paymentWxDM = PaymentWxDModel::getInstance();

        /** @var PaymentWx $paymentWx */
        $paymentWx = $paymentWxDM->findOneBy(array("tradeNo" => $result["out_trade_no"]));

        if (!$paymentWx) {
            $notify->setReturnParameter("return_msg", "无此订单号"); //设置返回微信的xml数据--返回信息
            $returnXml = $notify->returnXml();//将失败状态的xml数据返回微信，微信将重新发送通知
            return $this->getResponse($returnXml);
        }


        if ($paymentWx->getStatus() == 1) {
            $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回微信的xml数据
            $notify->setReturnParameter("return_msg", "OK"); //设置返回微信的xml数据
            $returnXml = $notify->returnXml();//告知微信，通知并验证成功
            return $this->getResponse($returnXml);
        }

        if ($paymentWx->getRelateTable() == "ServiceOrder") {

            return $this->serviceOrder($paymentWx, $notify, $result);
        }


    }

    private function serviceOrder(PaymentWx $paymentWx, \Notify_pub $notify, $result) {

        $id = $paymentWx->getRelateId();

        $serviceOrderDM = ServiceOrderDModel::getInstance();


        /** @var ServiceOrder $order */

        $order = $serviceOrderDM->find($id);

        if (!$order || $order->getStatus() == 1) {
            $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回微信的xml数据
            $notify->setReturnParameter("return_msg", "OK"); //设置返回微信的xml数据
            $returnXml = $notify->returnXml();//告知微信，通知并验证成功
            return $this->getResponse($returnXml);
        }

        $serviceDM = ServiceDModel::getInstance();


        /** @var Service $service */

        $service = $serviceDM->find($order->getServiceId());

        if (!$service) {
            $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回微信的xml数据
            $notify->setReturnParameter("return_msg", "OK"); //设置返回微信的xml数据
            $returnXml = $notify->returnXml();//告知微信，通知并验证成功
            return $this->getResponse($returnXml);
        }

        $sCode = explode("_", $service->getSCode());


        switch ($sCode[0]) {
            case  "workless":
                $companyServiceDM = CompanyServiceDModel::getInstance();
                if ($service->getTypes() == 99) { //定制版回调
                    $companyService = $companyServiceDM->newEntity();
                    $companyService->setSid($order->getSid());
                    $companyService->setNames($service->getNames());
                    $companyService->setSCode("workless");
                    $companyService->setTypes(99);
                    $companyService->setTotals($order->getNums());
                    $companyService->setServiceId($order->getServiceId());
                    $companyService->setUseTotals(0);
                    $companyService->setAddTime(nowTime());
                    $companyService->setExpireTime(nowTime(strtotime("+365 days")));
                    $companyService->setStatus(1);
                    $companyServiceDM->add($companyService)->flush($companyService);
                } else {
                    $companyService = $companyServiceDM->getWorklessService($order->getSid());

                    switch ($order->getTypes()) {
                        case 1: //购买套餐回调
                            $companyService->setNames($service->getNames());
                            $companyService->setSCode("workless");
                            $companyService->setTypes(3);
                            $companyService->setServiceId($service->getId());
                            $companyService->setTotals($order->getNums());
                            $companyService->setAddTime(nowTime());
                            $companyService->setExpireTime(nowTime(strtotime("+365 days")));
                            break;
                        case 2: //扩容回调
                            $companyService->setTotals($order->getNums() + $companyService->getTotals());
                            break;
                        case 3: //升级回调
                            $companyService->setNames($service->getNames());
                            $companyService->setServiceId($service->getId());
                            break;
                        case 4: //续期回调
                            $expireTime = $companyService->getExpireTime();
                            $time = time();
                            if (!$expireTime || $expireTime->getTimestamp() < $time) {
                                $companyService->setExpireTime(nowTime(strtotime("+365 days")));
                            } else {
                                $companyService->setExpireTime(nowTime(strtotime("+365 days", $expireTime->getTimestamp())));
                            }
                            break;
                    }
                    $companyService->setStatus(1);

                    $companyServiceDM->save($companyService)->flush($companyService);
                }


                break;
            case  "sms":

                $companyServiceDM = CompanyServiceDModel::getInstance();
                $companyService = $companyServiceDM->getSmsService($order->getSid());

                $companyService->setTotals($companyService->getTotals() + $order->getNums());
                $companyService->setStatus(1);

                $companyServiceDM->save($companyService)->flush($companyService);

                break;
        }

        $paymentWxDM = PaymentWxDModel::getInstance();

        $paymentWx->setStatus(1);

        $paymentWx->setTradeId($result['transaction_id']);
        //$paymentWx->setTradeCode();
        $paymentWxDM->save($paymentWx)->flush($paymentWx);

        $order->setStatus(1);
        $order->setPayTypes(1);
        $order->setDoneTime(nowTime());

        $serviceOrderDM->save($order)->flush($order);

        $notify->setReturnParameter("return_code", "SUCCESS"); //设置返回微信的xml数据
        $notify->setReturnParameter("return_msg", "OK"); //设置返回微信的xml数据
        $returnXml = $notify->returnXml();//告知微信，通知并验证成功
        return $this->getResponse($returnXml);

    }

}
