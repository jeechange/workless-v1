<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class AdminDModel extends DModel {

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
        return new \Admin\Entity\Admin();
    }


    public function checkLogin($username, $password) {
        /* @var $user \Admin\Entity\Admin */
        $user = $this->findOneBy(array("userName" => $username));
        if (!$user)
            $user = $this->findOneBy(array("tel" => $username));
        if (!$user) {
            $this->error = "用户不存在！";
            return false;
        }
        if ($user->getPassword() != sysmd5($password)) {
            $this->error = "密码不正确！";
            return false;
        }

        if ($user->getStatus() != 1) {
            $this->error = "用户未激活！";
            return false;
        }
        return $user;
    }


}