<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class TaskCommentDModel extends DModel {

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
            $user = UserDModel::getInstance()->find($result["userId"] ?: 0);
            $ruser = UserDModel::getInstance()->find($result["replyId"] ?: 0);
            $result["userName"] = $user ? $user->getFullName() : "【未知】";
            $result["ruserName"] = $ruser ? $ruser->getFullName() : "【未知】";
        }
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\TaskComment();
    }

    public function getComments($tid) {
        $lists = $this->name("c")->where("c.tid=$tid")->order("c.id")->getArray();


        $comments = array();
        if (!$lists) return $comments;

        foreach ($lists as $item) {
            $comments[$item["aid"]][] = $item;
        }

        return $comments;

    }


}