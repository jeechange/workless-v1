<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

/**
 * 他评表
 * Class TargetOthersDModel
 * @package Admin\DModel
 */
class TargetOthersDModel extends DModel {
//userId:他评id
//tdId:目标详情id
//score:评分id
//status:评分状态

    private $statusMemo = array(
        "0" => "未评",
        "1" => "已评",
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

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\TargetOthers();
    }


}