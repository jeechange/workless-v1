<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class TaskDynamicDModel extends DModel {

    protected $cdnBase = "https://cdn.itmakes.com/uploads";
    protected $cdnThumbBase = "https://cdn.itmakes.com/thumbs";


    public $typesMemo = array(
        0 => "发布",
        1 => "反馈",
        2 => "指派",
        3 => "完成",
        4 => "取消",
        5 => "结束",
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
            $user = UserDModel::getInstance()->find($result["userId"] ?: 0);
            $ruser = UserDModel::getInstance()->find($result["ruserId"] ?: 0);
            $result["userName"] = $user ? $user->getFullName() : "【未知】";
            $result["ruserName"] = $ruser ? $ruser->getFullName() : "【未知】";
            $result["thumbs"] = $this->parseThumbs($result["thumbs"]);
        }else{
            $result["thumbs"] = $this->parseThumbs($result["thumbs"]);
        }
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\TaskDynamic();
    }


    public function parseThumbs($thumb) {

        $thumbs = json_decode($thumb, true);
        $parseThumbs = array();

        if ($thumbs) {
            foreach ($thumbs as $thumb) {
                if (!$thumb[0]) continue;
                $parseThumbs[] = array(
                    "name" => $thumb[1] ?: basename($thumb[0]),
                    "src" => $this->cdnBase . $thumb[0],
                    "val" => $thumb[0],
                    "type" => $this->getThumbType($thumb[0])
                );
            }
        }
        return $parseThumbs;
    }

    private function getThumbType($name) {
        $ext = strtolower(substr(strrchr($name, "."), 1));

        if (in_array($ext, array("png", "jpg", "gif", "jpeg", "bmp"))) return "img";

        return "file";
    }


}