<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class SurveyResultDModel extends DModel {
//    字段解释
//surveyId:调查项目id
//teamId:调查组id
//userId: 调查小组人员id
//score:被调查用户id和评分josn存储
//total: 总分
//userScore:被评分人员名字和评分
//addTime:添加时间
//scoreTime: 评分时间
//status:是否已评分 0未评1已评

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
        return new \Admin\Entity\SurveyResult();
    }


}