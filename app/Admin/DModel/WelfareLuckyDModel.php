<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class WelfareLuckyDModel extends DModel {

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

        if ($this->scalar) {
            $result["scopesMemo"] = $this->getScopesMemo($result["l_scopes"]);
        }

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\WelfareLucky();
    }

    public function getScopesMemo($scopes) {
        if ($scopes == 0) return "全公司";
        $departmentDM = DepartmentDModel::getInstance();

        $departments = explode(",", $scopes);

        $returns = array();

        foreach ($departments as $department) {
            $departmentEN = $departmentDM->find($department ?: 0);
            if ($departmentEN) {
                $returns[] = $departmentEN->getNames();
            }
        }
        return join(",", $returns);

    }


}