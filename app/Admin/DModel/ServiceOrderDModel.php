<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class ServiceOrderDModel extends DModel {

    public $payTypes = array(
        1 => "微信"
    );

    public $typesMemo = array(
        1 => "购买",
        2 => "扩容",
        3 => "升级",
        4 => "续费",
    );

    public $statusMemo = array(
        0 => "未付款",
        1 => "已付款",
    );

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

        $result["serviceName"] = $this->getServiceName($result["serviceId"]);
        $result["serviceUnit"] = $this->getServiceUnit($result["serviceId"]);
        $result["typesMemo"] = $this->typesMemo[$result["types"]];
        $result["statusMemo"] = $this->statusMemo[$result["status"]];

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\ServiceOrder();
    }

    public function getServiceName($serviceId) {
        if (!$serviceId) return "";
        $serviceDM = ServiceDModel::getInstance();

        $service = $serviceDM->find($serviceId);

        return $service ? $service->getNames() : "";
    }

    public function getServiceUnit($serviceId) {

        if (!$serviceId) return "件";
        $serviceDM = ServiceDModel::getInstance();

        $service = $serviceDM->find($serviceId);

        if ($service) {
            $scodes = explode("_", $service->getSCode());
            switch ($scodes[0]) {
                case "sms":
                    return "条";
                case "workless":
                    return "人";
            }
        }
        return "件";

    }


}