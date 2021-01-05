<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class WelfareOrderDModel extends DModel {
    public $statusMemo = array(
        0 => "未核销",
        1 => "已核销",
        2 => "核销失败",
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
        if ($this->scalar) {
            $result["statusMemo"] = $this->statusMemo[$result["wo_status"]];

        } else {
            $result["statusMemo"] = $this->statusMemo[$result['status']];
        }

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\WelfareOrder();
    }


}