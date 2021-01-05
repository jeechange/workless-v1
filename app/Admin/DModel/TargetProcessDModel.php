<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

/**
 * 目标进程记录
 * Class TargetProcessDModel
 * @package Admin\DModel
 */
class TargetProcessDModel extends DModel {

//td_id 目标详情id
//process 存在问题
//proportion 占比

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
        return new \Admin\Entity\TargetProcess();
    }


}