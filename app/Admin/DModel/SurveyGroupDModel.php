<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class SurveyGroupDModel extends DModel {

//sid:公司id
//names:小组名称
//leader: 组长
//helper: 副组长
//members: 组员（存id格式如：1,2,3,4）
//addTime: 添加时间
//status: 状态
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
        return new \Admin\Entity\SurveyGroup();
    }
            
            
}