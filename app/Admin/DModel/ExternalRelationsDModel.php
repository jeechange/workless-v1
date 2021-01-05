<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class ExternalRelationsDModel extends DModel {

    public $statusMemo = array(
        "0" => "禁用",
        "1" => "启用",
        "2" => "禁用",
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
            $result["statusMemo"] = $this->statusMemo[$result["status"]] ?: "未知";
            $result["tgMemo"] = $this->tgName($result['tgId']);
        } else {
            $result["statusMemo"] = $this->statusMemo[$result["e_status"]] ?: "未知";
            $result["tgMemo"] = $this->tgName($result['e_tgId']);
        }
    }

    public function tgName($tgId) {
        if (!$tgId or $tgId <= 0) return "";

        $tgDM = TaskGroupDModel::getInstance();
        $tgLists = $tgDM->name("tg")->select("tg.names")->where("tg.id in ({$tgId})")->getArray();
        $arr = array();
        foreach ($tgLists as $v) {
            $arr[] = $v['names'];
        }
        $arr = implode(" | ", $arr);

        return $arr;
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\ExternalRelations();
    }


    public function UserId($userid, $phone) {
        $this->name("e")->select("e")->where("e.phone=" . $phone . " AND e.userId=0")->update(array("e.userId" => $userid));
    }


}