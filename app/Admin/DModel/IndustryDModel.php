<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class IndustryDModel extends DModel {

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
        return new \Admin\Entity\Industry();
    }


    public function getOptions($selectId, $pid = 0, $depth = 0) {
        $where = "i.parentid = $pid";
        $section = $this->name('i')->where($where)->order("i.sort", "desc")->getArray(false, false);
        if (!$section) {
            return "";
        }
        $options = "";
        $i = 0;
        $count = count($section);
        foreach ($section as $sec) {
            $selected = ($sec["id"] == $selectId) ? " selected" : "";
            $depthstr = $this->depth($i, $count, $depth);
            $options .= "<option value=\"{$sec["id"]}\"{$selected}>{$depthstr}{$sec["names"]}</option>";
            $options .= $this->getOptions($selectId, $sec["id"], $depth + 1);
            $i++;
        }
        return $options;
    }


    private function depth($i, $count, $depth) {
        if ($depth == 0) {
            return "";
        }
        $nbsp = '&nbsp;';
        $return = "";
        for ($j = 0; $j < $depth; $j++) {
            $return .= $nbsp;
        }
        return ($i + 1) < $count ? $return . "├" : $return . "└";
    }

    public function industryMemo($industryId) {

        $industry = $this->find($industryId ?: 0);
        if (!$industry) return "";

        return $industry->getNames();

    }


}