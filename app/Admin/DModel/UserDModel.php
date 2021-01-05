<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class UserDModel extends DModel {

    public $statusMemo = array(
        "0" => "未激活",
        "1" => "正常",
        "2" => "冻结",
    );


    /**
     * 自动填充规则
     */
    public function _fill() {
        $this->addFill("pwd", "sysmd5", self::FILL_FUNCTION, self::TYPE_INSERT);
        $this->addFill("pwd_confirm", "sysmd5", self::FILL_FUNCTION, self::TYPE_INSERT);
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
            $result["statusMemo"] = $this->getStatusMemo($result["u_status"]);
        }

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\User();
    }

    public function checkLogin($username, $password) {
        /* @var $user \Admin\Entity\User */
        $user = $this->findOneBy(array("userName" => $username));
        if (!$user)
            $user = $this->findOneBy(array("phone" => $username));
        if (!$user) {
            $this->error = "用户不存在！";
            return false;
        }
        if ($password !== null) {
            if ($user->getPwd() != sysmd5($password)) {
                $this->error = "密码不正确！";
                return false;
            }
        }

        if ($user->getStatus() != 1) {
            $this->error = "用户未激活！";
            return false;
        }
        return $user;
    }

    public function getStatusMemo($status) {
        return $this->statusMemo[$status] ?: "未知";
    }


    public function getUserName($id) {
        $lists = $this->name("u")->select("u")->where("u.id=" . $id)->getOneArray();
        return $lists['fullName'] ?: "";
    }

}