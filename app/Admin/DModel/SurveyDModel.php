<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class SurveyDModel extends DModel {
//字段注释
//sid:公司id
//scId: 维度
//code: 编号
//standId:维度标准
//surveyObject: 调查对象
//surveyTeam: 调查组
//addTime:添加的时间
//startTime:开始时间
//endTime:结束时间
//issue:发布人id
//type:调查类型
//status:状态
//totalScore:总分
//anonymity: 匿名
//tags: //名字/项目关键词

    protected $typeMemo = array(
        "0" => "线上",
        "1" => "线下"
    );
    protected $anonymityMemo = array(
        "0" => "匿名",
        "1" => "显示"
    );
    protected $statusMemo = array(
        "0" => "未开始",
        "1" => "未完成",
        "2" => "已完成",
        "3" => "已过期",
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
            $result["typeMemo"] = $this->getTypeMemo($result["type"]);
            $result["anonymityMemo"] = $this->getAnonymityMemo($result["anonymity"]);
            $result["executorMemo"] = $this->executorMemo($result["surveyObject"]);
        } else {
            $result["statusMemo"] = $this->getStatusMemo($result["s_status"]);
            $result["typeMemo"] = $this->getTypeMemo($result["s_type"]);
            $result["anonymityMemo"] = $this->getAnonymityMemo($result["s_anonymity"]);
            $result["executorMemo"] = $this->executorMemo($result["s_surveyObject"]);
        }

    }


    public function getStatusMemo($result) {
        return $this->statusMemo[$result] ?: "未知";
    }

    public function getTypeMemo($result) {
        return $this->typeMemo[$result] ?: "未知";
    }

    public function getAnonymityMemo($result) {
        return $this->anonymityMemo[$result] ?: "未知";


    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\Survey();
    }

    public function executorMemo($executors) {
        if (!$executors) return "";

        $executorIds = explode(",", $executors);
        $staffDM = UserDModel::getInstance();
        $users = array();

        foreach ($executorIds as $id) {
            $user = $staffDM->find($id);
            if ($user) $users[] = $user->getFullName();
        }
        return join(",", $users);
    }

}