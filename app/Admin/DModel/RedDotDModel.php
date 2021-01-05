<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class RedDotDModel extends DModel {

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
        return new \Admin\Entity\RedDot();
    }

    /**
     * 用户ID，公司ID，来源ID，来源表名
     */
    public function NewAdd($userId, $sid, $formId, $formName) {
        $rdEN = $this->findBy(array("userId" => $userId, "sid" => $sid, "formId" => $formId, "formName" => $formName));
        if (!$rdEN) {
            $redDotEN = $this->newEntity();
            $redDotEN->setSid($sid);
            $redDotEN->setUserId($userId);
            $redDotEN->setFormId($formId);
            $redDotEN->setFormName($formName);
            $redDotEN->setStatus(1);
            $this->add($redDotEN)->flush($redDotEN);
        }

        return true;
    }

}