<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class CompanyServiceDModel extends DModel {


    /**
     * 自动填充规则
     */
    public function _fill() {
        //$this->addFill("pwd", "sysmd5", self::FILL_FUNCTION, self::TYPE_INSERT);  //自动填充示例
    }


    /**
     * 自动验证规则
     */
    public function _check() {
        //$this->addRule("names", self::RULE_UNIQUE, "名称必须唯一", "", self::CHECK_NEED, self::TYPE_BOTH);//自动验证示例       
    }

    protected function resolveArray(&$result) {

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\CompanyService();
    }

    public function getSmsService($sid) {
        $service = $this->findOneBy(array("sCode" => "sms", "sid" => $sid));

        if ($service) return $service;
        $service = $this->newEntity();
        $service->setSid($sid);
        $service->setNames("短信费");
        $service->setSCode("sms");
        $service->setTypes(1);
        $service->setTotals(0);
        $service->setServiceId(0);
        $service->setUseTotals(0);
        $service->setAddTime(nowTime());
        $service->setStatus(0);
        $this->add($service)->flush($service);
        return $service;
    }

    public function getWorklessService($sid) {

        $service = $this->findOneBy(array("sCode" => "workless", "sid" => $sid, "types" => 3));
        if ($service) return $service;

        $companyMemberDM = CompanyMemberDModel::getInstance();

        $memberCount = $companyMemberDM->name("cm")->where("cm.sid=$sid")->count();

        $service = $this->newEntity();
        $service->setSid($sid);
        $service->setNames("workless团队版");
        $service->setSCode("workless");
        $service->setTypes(3);
        $service->setServiceId(0);
        $service->setTotals(9);
        $service->setUseTotals($memberCount);
        $service->setAddTime(nowTime());
        $service->setStatus(0);
        $this->add($service)->flush($service);
        return $service;

    }

    public function getWorklessServiceName($serviceId) {
        if (!$serviceId) return "免费体验版";

        $serviceDM = ServiceDModel::getInstance();

        $service = $serviceDM->find($serviceId);

        if (!$service) return "未知版本";

        return $service->getNames();
    }

    public function getWorklessServiceMoney($serviceId) {
        if (!$serviceId) return 0;

        $serviceDM = ServiceDModel::getInstance();

        $service = $serviceDM->find($serviceId);

        if (!$service) return 0;

        return $service->getMoney();
    }


}