<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class TargetDModel extends DModel {

//userId: 用户
//names:目标阶段名称（可选填，后期可能用来查询）
//addTime:添加时间
//startTime:目标开始考核时间
//endTime: 目标结束考核时间
//remind: 提醒时间阶段，用josn存，如：time1：'2019-01-15 12:12:30',time2：'2019-01-30 12:12:30'
//auditor:审核人，最多三个审核，多个userid，用英文逗号','隔开
//status: 状态
//types:目标类型
//menber: 目标对象
//times：目标时间
//othersStaff：他评成员，多个userid，用英文逗号','隔开
//sid:公司ID
//mNames:对象名字
//aNames:审核人名字
//oNames:他评成员名字
//aId 弃用

    private $typesMemo = array(
        0 => "月度",
        1 => "季度",
        2 => "半年度",
        3 => "年度",
        4 => "试用期",
    );

    private $statusMemo = array(
        0 => "未完成",
        1 => "已完成",
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
            $result["typesMemo"] = $this->getTypeMemo($result["types"]);
        } else {
            $result["statusMemo"] = $this->getStatusMemo($result["t_status"]);
            $result["typesMemo"] = $this->getTypeMemo($result["t_types"]);
        }
    }

    public function getStatusMemo($result) {
        return $this->statusMemo[$result] ?: "未知";
    }

    public function getTypeMemo($result) {
        return $this->typesMemo[$result] ?: "未知";
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\Target();
    }


}