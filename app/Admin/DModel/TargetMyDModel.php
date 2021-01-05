<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

/**
 * 被考核对象表
 * Class TargetMyDModel
 * @package Admin\DModel
 */
class TargetMyDModel extends DModel {

    protected $statusMemo = array(
        "0" => "待加入",
        "1" => "待审核",
        "2" => "执行中",
        "3" => "待自评",
        "4" => "待他评",
        "5" => "已考核",
        "6" => "审核失败",
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
            $result["statusMemo"] = $this->getStatusMemo($result["status"]);
        } else {
            $result["statusMemo"] = $this->getStatusMemo($result["mt_status"]);
            $result["statusMemo1"] = $this->getStatusMemo($result["tm_status"]);
        }
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\TargetMy();
    }

    public function getStatusMemo($result) {
        return $this->statusMemo[$result] ?: "未知";
    }

    public function getALLStatusMemo() {
        return $this->statusMemo;
    }

}