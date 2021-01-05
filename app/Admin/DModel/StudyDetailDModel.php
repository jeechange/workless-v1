<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class StudyDetailDModel extends DModel {
    private $statusMemo = array(
        "0" => "未完成",
        "1" => "已完成"
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
            $result['statusMemo'] = $this->getStatusMemo($result['status']);
        } else {
            $result['statusMemo'] = $this->getStatusMemo($result['sd_status']);
            $result["recHtmlMemo"] = str_repeat("<i class='icon-star'></i>", $result["s_rec"]);
            $result["auditUsers"] = $this->auditUsers($result["s_auditUser"]);
        }
    }

    public function getStatusMemo($result) {
        return $this->statusMemo[$result] ?: "未知";
    }
    public function auditUsers($auditUsers) {
        if (!$auditUsers) return "";

        $executorIds = explode(",", $auditUsers);
        $userDM = UserDModel::getInstance();
        $users = array();

        foreach ($executorIds as $id) {
            $user = $userDM->find($id);
            if ($user) $users[] = $user->getFullName();
        }

        return join(",", $users);
    }
    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\StudyDetail();
    }


}