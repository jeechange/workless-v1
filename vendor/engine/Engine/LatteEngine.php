<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Engine;

/**
 * Description of LatteEngine
 *
 * @author Administrator
 */
class LatteEngine {

    private $appName;
    protected $pattern = "/^(?<app>[a-zA-Z0-9_]+@)?(?<theme>[a-zA-Z0-9_]+#)?(?<con>[a-zA-Z0-9_]+[\\:\\/])?(?<act>[a-zA-Z0-9_]+)?$/";

    public function fetch($templateFile, $assign, $config) {

        $latte = new \Latte\Engine;
        $latte->setAutoRefresh(defined("LATTE_DEV")?LATTE_DEV:main()->getEnv() == "dev");
        preg_match("/\.(?P<type>html|xhtml|xml|js|css|url|ical|text)\.latte$/", $templateFile, $match);
        if ($match)
            $latte->setContentType($match["type"]);
        $cache = $this->appName ? $config['cache'] . "latte/" . $this->appName : $config['cache'] . "latte";
        if (!is_dir($cache))
            mkdir($cache, 0777, true);
        $latte->setTempDirectory($cache);
        $lattes = (array) C("view.extension");
        phpexEngine::installExtend($latte);
        foreach ($lattes as $ilatte) {
            if (class_exists($ilatte)) {
                $rf = new \ReflectionClass($ilatte);
                if ($rf->isSubclassOf(__NAMESPACE__ . "\\ILatte"))
                    $ilatte::installExtend($latte);
            }
        }

        return $latte->renderToString($templateFile, $assign);
    }

    public function setAppName($appName) {
        $this->appName = $appName;
    }



}
