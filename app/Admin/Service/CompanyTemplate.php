<?php

namespace Admin\Service;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/10/10
 * Time: 11:30
 */
class CompanyTemplate {

    public function getLists($all = false) {

        $basePath = main()->getAppRoot() . "/config/CompanyTemplate/";
        $files = glob($basePath . "*.yml");

        $templates = array();
        foreach ($files as $file) {
            $fileName = basename($file);
            $config = parseFile($file);
            $templates[] = $all ? array($fileName, $config["names"], $config) : array($fileName, $config["names"]);
        }
        return $templates;
    }


    public function import($sid, $fileName) {
        if (!$sid || !$fileName) return;

        $basePath = main()->getAppRoot() . "/config/CompanyTemplate/";
        $filePath = $basePath . $fileName;

        if (!is_file($filePath)) return;

        $config = parseFile($filePath);
        if (!$config) return;
        if ($config["standards"]) {
            $standardDM = \Admin\DModel\StandardDModel::getInstance();
            foreach ($config["standards"] as $standard) {
                $standardEN = $standardDM->findOneBy(array("names" => $standard[0], "classify" => $standard[1], "sid" => $sid));
                if (!$standardEN) $standardEN = $standardDM->newEntity();

                $standardEN->setNames($standard[0]);
                $standardEN->setClassify($standard[1]);
                $standardEN->setAcorn($standard[2]);
                $standardEN->setMethods($standard[3]);
                $standardEN->setCycle($standard[4]);
                $standardEN->setMethods($standard[5]);
                $standardEN->setSNo($standard[6]);
                $standardEN->setSubClassify(0);
                $standardEN->setSid($sid);
                $standardEN->setStatus(1);
                $standardEN->setAddTime(nowTime());
                $standardEN->setHot(0);
                $standardEN->setTypes($standard[7]);
                $standardDM->add($standardEN)->flush($standardEN);
            }
        }
    }

    public function export($fromSid = 0, $format = "yml", $return = false) {

        $standardDM = \Admin\DModel\StandardDModel::getInstance();

        $standards = array();

        $lists = $standardDM->name("s")->where("s.sid=$fromSid")->getArray();

        if (!$lists) {
            if ($return) return false;
            dump("");
            exit;
        }
        // [0names,1classify,2acorn,3methods,4cycle,5memo,6s_no]
        foreach ($lists as $item) {
            $standards[] = array(
                0 => $item["names"],
                1 => $item["classify"],
                2 => $item["acorn"],
                3 => $item["methods"],
                4 => $item["cycle"],
                5 => $item["memo"] ?: "",
                6 => $item["s_no"] ?: ""
            );
        }


        $res = arrDump($standards, $format, 1);

        if ($return) return $res;

        dump($res);
        exit;
    }

}
