<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class TargetOrgDetailDModel extends DModel {
    public $typesMemo = array(
        "0" => "月度目标",
        "1" => "季度目标",
        "2" => "半年度目标",
        "3" => "年度目标",
        "4" => "使命",
        "5" => "愿景",
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
        if (!$this->scalar) {
            $result["typesMemo"] = $this->getTypesMemo($result["types"]);
        } else {
            $result["typesMemo"] = $this->getTypesMemo($result["tod_types"]);
        }

    }

    protected function resolveObject($result = null) {

    }

    public function getTypesMemo($types = "") {
        return $this->typesMemo[$types] ?: $this->typesMemo;
    }



    public function newEntity() {
        return new \Admin\Entity\TargetOrgDetail();
    }


}