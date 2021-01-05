<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class CompanyAuthDModel extends DModel {

    public $statusMemo = array(
        0 => "审核中",
        1 => "已认证",
        2 => "认证不通过",
        3 => "未认证",
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
            $result["scalesMemo"] = CompanyDModel::getInstance()->memos["scales"][$result["scales"]];
            $result["statusMemo"] = $this->statusMemo[$result["status"]];
        } else {
            $result["scalesMemo"] = CompanyDModel::getInstance()->memos["scales"][$result["a_scales"]];
            $result["statusMemo"] = $this->statusMemo[$result["a_status"]];
        }

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\CompanyAuth();
    }


}