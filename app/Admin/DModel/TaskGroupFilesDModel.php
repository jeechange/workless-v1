<?php

namespace Admin\DModel;

use phpex\DModel\DModel;

class TaskGroupFilesDModel extends DModel {

    public $typesMemo = array(
        0 => "文件夹",
        1 => "文档",
        2 => "图片",
        3 => "压缩文件",
        4 => "音频文件",
        5 => "视频文件",
        99 => "其它文件",
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
        $result["typesMemo"] = $this->getTypesMemo($result["types"], $result["suffix"]);
    }

    protected function resolveObject($result = null) {

    }

    public function newEntity() {
        return new \Admin\Entity\TaskGroupFiles();
    }

    public function getTypesMemo($types, $suffix) {
        if ($types == 0) return $this->typesMemo[0];

        $docFiles = array("txt", "doc", "xls", "docx", "xlsx", "ppt", "pptx", "pdf");

        if (in_array(strtolower($suffix), $docFiles)) {
            return $this->typesMemo[1];
        }

        $imgFiles = array("jpg", "jpeg", "png", "bmp", "gif");

        if (in_array(strtolower($suffix), $imgFiles)) {
            return $this->typesMemo[2];
        }
        $rarFiles = array("rar", "zip", "7z", "gz", "tar");
        if (in_array(strtolower($suffix), $rarFiles)) {
            return $this->typesMemo[3];
        }

        $mFiles = array("mp3", "wma",);

        if (in_array(strtolower($suffix), $mFiles)) {
            return $this->typesMemo[4];
        }

        $videoFiles = array("rm", "rmvb", "wmv", "avi", "mp4", "3gp", "mkv", "mpeg", "mov", "flv", "f4v", "m4v", "dat", "ts", "mts", "vob", "asf", "asx");

        if (in_array(strtolower($suffix), $videoFiles)) {
            return $this->typesMemo[5];
        }

        return $this->typesMemo[99];

    }


}