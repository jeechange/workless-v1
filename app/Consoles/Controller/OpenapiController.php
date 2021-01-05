<?php

namespace Consoles\Controller;


use Admin\DModel\CompanyDModel;
use Admin\DModel\CompanyOpenapiDModel;
use Admin\DModel\CompanyServiceDModel;
use Admin\DModel\RbacAccessDModel;
use Admin\DModel\ServiceDModel;
use Admin\DModel\ServiceOrderDModel;
use Admin\DModel\SettingsDModel;
use Admin\Entity\Service;
use Admin\Entity\Settings;

class OpenapiController extends CommonController {

    public function dingtalk() {
        $apiDM = CompanyOpenapiDModel::getInstance();
        $api = $apiDM->findOneBy(array("sid" => $this->sid, "namesEn" => "dingtalk"));
        if (Q()->isGet()) {
            if ($api) {
                $this->assign("api", $apiDM->toArray($api));
            }
            $companyDM = CompanyDModel::getInstance();
            $company = $companyDM->find($this->sid);

            $this->assign("codeNo", $company ? $company->getCodeNo() : "");
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$api) $api = $apiDM->newEntity();

        $api->setStatus(1);
        $api->setSid($this->sid);
        $api->setNames("钉钉");
        $api->setNamesEN("dingtalk");
        $api->setAgentid($post["agentid"]);
        $api->setCorpid($post["corpid"]);
        $api->setCorpsecret($post["corpsecret"]);

        $apiDM->add($api)->flush($api);
        return $this->success("修改成功");
    }

    public function wxwork() {
        $apiDM = CompanyOpenapiDModel::getInstance();
        $api = $apiDM->findOneBy(array("sid" => $this->sid, "namesEn" => "wxwork"));
        if (Q()->isGet()) {
            if ($api) {
                $this->assign("api", $apiDM->toArray($api));
            }
            $companyDM = CompanyDModel::getInstance();
            $company = $companyDM->find($this->sid);

            $this->assign("codeNo", $company ? $company->getCodeNo() : "");
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$api) $api = $apiDM->newEntity();

        $api->setStatus(1);
        $api->setSid($this->sid);
        $api->setNames("企业微信");
        $api->setNamesEN("wxwork");
        $api->setAgentid($post["agentid"]);
        $api->setCorpid($post["corpid"]);
        $api->setCorpsecret($post["corpsecret"]);

        $apiDM->add($api)->flush($api);
        return $this->success("修改成功");
    }

    public function dingWebhook() {
        $apiDM = CompanyOpenapiDModel::getInstance();
        $lists = $apiDM->name("a")->where("a.sid=:sid and a.namesEn = :namesEn")->setParameter(array("sid" => $this->sid, "namesEn" => "dingwebhook"))->getArray();
        $this->assign("lists", $lists);
        return $this->display();
    }

    public function dingWebhookAdd() {
        if (Q()->isGet()) {
            return $this->display();
        }

        $post = Q()->post->all();

        $apiDM = CompanyOpenapiDModel::getInstance();

        $old = $apiDM->findOneBy(array("sid" => $this->sid, "corpsecret" => $post["corpsecret"], "namesEn" => "dingwebhook"));

        if ($old) {
            return $this->error("不能重复添加同一个Webhook地址的机器");
        }

        $api = $apiDM->newEntity();

        $api->setStatus($post["status"]);
        $api->setSid($this->sid);
        $api->setNames($post["names"]);
        $api->setNamesEN("dingwebhook");
        // $api->setAgentid($post["agentid"]);
        //$api->setCorpid($post["corpid"]);
        $api->setCorpsecret($post["corpsecret"]);

        $apiDM->add($api)->flush($api);
        return $this->success("添加成功");

    }

    public function dingWebhookModify($id) {
        $apiDM = CompanyOpenapiDModel::getInstance();
        $api = $apiDM->find($id);
        if (!$api || $api->getNamesEn() != "dingwebhook" || $api->getSid() != $this->sid) {
            return $this->error("记录获取失败");
        }
        if (Q()->isGet()) {
            $this->assign("api", $api);
            return $this->display();
        }

        $post = Q()->post->all();

        $old = $apiDM->findOneBy(array("sid" => $this->sid, "corpsecret" => $post["corpsecret"], "namesEn" => "dingwebhook"));

        if ($old && $old->getId() != $id) {
            return $this->error("不能重复添加同一个Webhook地址的机器");
        }

        $api->setStatus($post["status"]);
        $api->setSid($this->sid);
        $api->setNames($post["names"]);
        $api->setNamesEN("dingwebhook");
        //$api->setAgentid($post["agentid"]);
        //$api->setCorpid($post["corpid"]);
        $api->setCorpsecret($post["corpsecret"]);

        $apiDM->add($api)->flush($api);
        return $this->success("修改成功");
    }

    /**
     * 删除钉钉机器人
     * @param $id
     * @return \phpex\Foundation\Response
     */
    public function dingWebhookDelete($id) {
        $apiDM = CompanyOpenapiDModel::getInstance();
        $api = $apiDM->find($id);
        if (!$api || $api->getNamesEn() != "dingwebhook" || $api->getSid() != $this->sid) {
            return $this->error("记录获取失败");
        }
        $apiDM->remove($api)->flush($api);
        return $this->success("删除成功", url("consoles_openapi_dingwebhook"));
    }

    public function email() {

        $settingsDM = SettingsDModel::getInstance();

        /** @var Settings $settings */
        $settings = $settingsDM->findOneBy(array("sid" => $this->sid, "names" => "email"));
        if (Q()->isGet()) {
            if ($settings) {
                $configs = $settings->getSettings() ? json_decode($settings->getSettings(), true) : array();
                $api = array(
                    "status" => $settings->getStatus(),
                    "address" => $configs["address"],
                    "password" => $configs["password"],
                    "names" => $configs["names"],
                    "smtp" => $configs["smtp"],
                    "signature" => $configs["signature"],
                );
                $this->assign("api", $api);
            }
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$settings) $settings = $settingsDM->newEntity();


        $settingConfig = array(
            "address" => $post["address"],
            "password" => $post["password"],
            "names" => $post["names"],
            "smtp" => $post["smtp"],
            "signature" => $post["signature"],
        );

        $settings->setStatus($post["status"] == 1 ? 1 : 0);
        $settings->setSid($this->sid);
        $settings->setNames("email");
        $settings->setSettings(json_encode($settingConfig));


        $settingsDM->add($settings)->flush($settings);
        return $this->success("修改成功");

    }

    public function sms() {
        $companyServiceDM = CompanyServiceDModel::getInstance();


        $service = $companyServiceDM->getSmsService($this->getUser("sid"));

        $this->assign("service", $service);

        return $this->display();
    }


    public function smsBuy() {
        $serviceDM = ServiceDModel::getInstance();

        if (Q()->isGet()) {
            $lists = $serviceDM->name("s")->where("s.sCode like 'sms%'")->getArray();
            $this->assign("lists", $lists);
            return $this->display();
        }

        $buyId = Q()->post->get("buyId") ?: 0;

        if (!$buyId) return $this->ajaxReturn(array("status" => "n", "info" => "请选择购买的套餐"));

        /** @var Service $service */
        $service = $serviceDM->find($buyId);

        if (!$service || $service->getStatus() != 1) return $this->ajaxReturn(array("status" => "n", "info" => "套餐不存在或已下架"));

        $scodes = explode("_", $service->getSCode());

        if (!$scodes || $scodes[0] != "sms") return $this->ajaxReturn(array("status" => "n", "info" => "套餐信息异常"));

        $serviceOrderDM = ServiceOrderDModel::getInstance();

        $order = $serviceOrderDM->newEntity();

        $sid = $this->getUser("sid");

        $order->setSid($sid);
        $order->setServiceId($buyId);
        $order->setTypes(1);
        $order->setOrderId(sprintf("%s%s", date("YmdHis"), rand_string(4, 1)));
        $order->setAddTime(nowTime());
        $order->setMoney($service->getMoney());
        $order->setNums($service->getSpec());
        $order->setStatus(0);
        $serviceOrderDM->add($order)->flush($order);

        $session = Q()->getSession();

        $user = $this->getUser();

        $session->switchAppSession("Home");

        RbacAccessDModel::accredit($this->access, "Home", $user->getSid(), $user, $user->getRoleName());

        return $this->ajaxReturn(array("status" => "y", "info" => "下单成功", "nextUrl" => url("home_index_orderConfirm", array("id" => $order->getId()))));
    }


    public function sms_bak() {


        $companyServiceDM = CompanyServiceDModel::getInstance();

        $settingsDM = SettingsDModel::getInstance();

        /** @var Settings $settings */
        $settings = $settingsDM->findOneBy(array("sid" => $this->sid, "names" => "sms"));
        if (Q()->isGet()) {
            if ($settings) {
                $configs = $settings->getSettings() ? json_decode($settings->getSettings(), true) : array();
                $api = array(
                    "status" => $settings->getStatus(),
                    "username" => $configs["username"],
                    "password" => $configs["password"],
                    "SIGNATURE" => $configs["SIGNATURE"],
                );
                $this->assign("api", $api);
            }
            return $this->display();
        }

        $post = Q()->post->all();

        if (!$settings) $settings = $settingsDM->newEntity();


        $settingConfig = array(
            "url" => "http://api.sms.cn/mtutf8/",
            "username" => $post["username"],
            "password" => $post["password"],
            "SIGNATURE" => $post["SIGNATURE"],
            "status" => $post["status"] == 1 ? 1 : 0,
        );

        $settings->setStatus($post["status"] == 1 ? 1 : 0);
        $settings->setSid($this->sid);
        $settings->setNames("sms");
        $settings->setSettings(json_encode($settingConfig));


        $settingsDM->add($settings)->flush($settings);
        return $this->success("修改成功");

    }

}
