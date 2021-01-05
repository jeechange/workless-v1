<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class TaskSettingDModel extends DModel {


    public $typesCount = array(1 => 4, 2 => 4, 3 => 2, 4 => 2, 5 => 2, 6 => 3, 7 => 5);
    public $defaultIcons = array(
        "/xiangshuyun/console/xiangshuyun/1_0/image/other/201807/1532073764e08d45f2e6b8e65a.png",
        "/xiangshuyun/console/xiangshuyun/1_0/image/other/201807/1532073733b5482b903771c59d.png",
        "/xiangshuyun/console/xiangshuyun/1_0/image/other/201807/1532073718de4fce3d4a52a158.png",
        "/xiangshuyun/console/xiangshuyun/1_0/image/icon/201807/15320719299f0a2f4d28b4a91a.png",
    );
    public $defaultNames = array(
        1 => array("优秀", "良好", "不理想", "未完成"),
        2 => array("白金勋章", "黄金勋章", "银牌勋章", "铜牌勋章"),
        3 => array("30", "20"),
        4 => array("0.6", "1.5"),
        5 => array("60", "130"),
        6 => array("10", "60", "130"),
        7 => array("50", 1, 100,100,100),
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

    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\TaskSetting();
    }


    public function getLists($sid, $types, $returnType = "array") {
        if (!isset($this->typesCount[$types]) || !$sid) return array();
        $lists = array();
        for ($i = 0; $i < $this->typesCount[$types]; $i++) {
            $item = $this->getRow($sid, $types, $i);
            if ($returnType == "array") {
                $lists[$i] = array(
                    "id" => $item->getId(),
                    "sid" => $sid,
                    "types" => $types,
                    "names" => $item->getNames(),
                    "icon" => $item->getIcon(),
                    "sort" => $item->getSort(),
                    "status" => $item->getStatus(),
                );
            } else {
                $lists[$i] = $item;
            }

        }
        return $lists;
    }

    public function getIdLists($sid, $types, $returnType = "array") {
        if (!isset($this->typesCount[$types]) || !$sid) return array();
        $lists = array();
        for ($i = 0; $i < $this->typesCount[$types]; $i++) {
            $item = $this->getRow($sid, $types, $i);
            if ($returnType == "array") {
                $lists[$item->getId()] = array(
                    "id" => $item->getId(),
                    "sid" => $sid,
                    "types" => $types,
                    "names" => $item->getNames(),
                    "icon" => $item->getIcon(),
                    "sort" => $item->getSort(),
                    "status" => $item->getStatus(),
                );
            } else {
                $lists[$i] = $item;
            }

        }
        return $lists;
    }


    public function getRow($sid, $types, $sort) {
        $item = $this->findOneBy(array("sid" => $sid, "types" => $types, "sort" => $sort));
        if (!$item) {
            $item = $this->newEntity();
            $item->setSid($sid);
            $item->setNames($this->defaultNames[$types][$sort]);
            $item->setTypes($types);
            $item->setIcon($this->defaultIcons[$sort]);
            $item->setSort($sort);
            $item->setStatus(1);
            $this->add($item)->flush($item);
        }
        return $item;
    }

    public function getAcorns($sid) {

        $lists3 = $this->getLists($sid, 3);
        $lists4 = $this->getLists($sid, 4);
        $lists5 = $this->getLists($sid, 5);

        $base = $lists3[0]["names"];

        $min4 = $lists4[0]["names"];
        $max4 = $lists4[1]["names"];

        $min5 = $lists5[0]["names"];
        $max5 = $lists5[1]["names"];
        return array(round($base * $min4 * $min5 / 100), round($base * $max4 * $max5 / 100));
    }

}