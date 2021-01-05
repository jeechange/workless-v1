<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class RbacRoleDModel extends DModel {

    public $statusMemo = array(
        0 => "停用",
        1 => "启用",
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
        $result["statusMemo"] = $this->statusMemo[$result["status"]];
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\RbacRole();
    }


    public function getRoles($sid, $module) {
        $lists = $this->name("r")->where("r.sid = $sid and r.module = '$module' and r.status=1")->order("r.sort", "ASC")->getArray();

        return $lists;
    }


}