<?php

namespace Node\Controller;

class IndexController extends CommonController {

    public function index() {
        //控制器示例代码
        return $this->display();
    }


    public function cp() {
        $res_root = dirname(__DIR__) . "/Resource";

        $dmodels = glob($res_root . "/dmodel/*.php.r");
        $entitys = glob($res_root . "/entity/*.php.r");
        $doctrines = glob($res_root . "/doctrine/*.yml.r");

        $dmodel_root = main()->getAppRoot() . "/Admin/DModel";
        $entity_root = main()->getAppRoot() . "/Admin/Entity";
        $doctrine_root = main()->getAppRoot() . "/Admin/Conf/doctrine";
        if (!is_dir($dmodel_root)) mkdir($dmodel_root, 0777, true);
        if (!is_dir($entity_root)) mkdir($entity_root, 0777, true);
        if (!is_dir($doctrine_root)) mkdir($doctrine_root, 0777, true);

        foreach ($dmodels as $file) {
            $basename = substr(basename($file), 0, -2);
            $target_name = $dmodel_root . "/" . $basename;
            if (is_file($target_name)) unlink($target_name);
            copy($file, $target_name);
        }
        foreach ($entitys as $file) {
            $basename = substr(basename($file), 0, -2);
            $target_name = $entity_root . "/" . $basename;
            if (is_file($target_name)) unlink($target_name);
            copy($file, $target_name);
        }
        foreach ($doctrines as $file) {
            $basename = substr(basename($file), 0, -2);
            $target_name = $doctrine_root . "/" . $basename;
            if (is_file($target_name)) unlink($target_name);
            copy($file, $target_name);
        }


        return $this->getResponse("复制成功");

    }
}