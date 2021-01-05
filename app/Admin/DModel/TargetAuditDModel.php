<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

/**
 * 审核目标表
 * Class TargetAuditDModel
 * @package Admin\DModel
 */
class TargetAuditDModel extends DModel {
//tId:目标id
//userId: 审核人
//status:审核状态

    private $statusMemo = array(
        "0" => "未审核",
        "1" => "已审核",
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
        return new \Admin\Entity\TargetAudit();
    }


}