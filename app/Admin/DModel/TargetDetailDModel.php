<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

/**
 * 被考核对象添加目标的表
 * Class TargetDetailDModel
 * @package Admin\DModel
 */
class TargetDetailDModel extends DModel {

//tId:发布目标id
//scId: 目标维度
//userId: 填写目标的用户
//names:目标名称
//percent:目标占比
//selfEval: 自评分
//othersEval: 他人评分
//process 存在问题（注：只存储最新那条记录作为显示使用）
//proportion  占比

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
        $this->addRule("names", self::RULE_REQUIRE, "名称不能为空", "", self::CHECK_NEED, self::TYPE_BOTH);//自动验证示例
    }

    protected function resolveArray(&$result) {
        if (!$this->scalar) {
            $result["standardSet"] = json_decode($result["standardSet"], true);
        } else {
            $result["td_standardSet"] = json_decode($result["td_standardSet"], true);
        }
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\TargetDetail();
    }


}