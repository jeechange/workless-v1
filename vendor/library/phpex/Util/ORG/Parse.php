<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Util\ORG;

use phpex\Util\Yaml\Yaml;
use phpex\Util\Xml\XmlParse;

/**
 * Description of Parse
 *
 * @author Administrator
 */
class Parse {

    private $cachePath;

    private function getCachePath() {
        if (null === $this->cachePath)
            $this->cachePath = main()->getRuntime() . "/Temp/parseFile";
        return $this->cachePath;
    }

    /**
     * 将指定文件解析成数组
     * @param type $path
     * @return boolean
     */
    public function parseFile($path) {
        if (is_file($path))
            $pathinfo = pathinfo($path);
        else {
            return array();
        }
        $parseCachePath = $this->getCachePath();
        if (!is_dir($parseCachePath)) {
            mkdir($parseCachePath, 0777, true);
        }
        $key = md5($path);
        $cachePath = $parseCachePath . "/" . $key . ".php";
        $handle = fopen($cachePath, "c+");
        if (!$handle) {
            E("Unable to open or create file '%s'", $cachePath);
        }
        $stat = fstat($handle);
        if ($stat["size"] > 0 && filemtime($path) <= $stat['mtime']) {
            return include $cachePath;
        }

        switch (strtolower($pathinfo['extension'])) {
            case "yml":
                $parse = $this->parseyaml($path);
                break;
            case "json":
                $parse = $this->parsejson($path);
                break;
            case "xml":
                $parse = $this->parsexml($path);
                break;
            case "php":
                $parse = $this->parsephp($path);
                break;
            default:
                $parse = false;
        }
        ftruncate($handle, 0);
        flock($handle, LOCK_EX | LOCK_NB);
        $content = sprintf("<?php\n //parse cache:%s \nreturn %s ;", $path, var_export($parse, true));
        fwrite($handle, $content, strlen($content));
        flock($handle, LOCK_UN);
        return $parse;
    }

    /**
     * 将指定字符串解析成数组
     * @param type $input
     * @param type $extension
     * @return array|boolean
     */
    function parseString($input, $extension = "yml") {
        switch (strtolower($extension)) {
            case "yml":
                return $this->parseyaml($input);
            case "json":
                return $this->parsejson($input);
            case "xml":
                return $this->parsexml($input);
            case "php":
                return $this->parsephp($input);
            default:
                return false;
        }
    }

    /**
     * 将指定字符串当yml解析成数组
     * @param type $input
     * @param type $type
     * @return array
     */
    function parseyaml($input, $type = 'file') {
        $configs = Yaml::load($input);
        return (array) $configs;
    }

    /**
     * 将指定字符串当json解析成数组
     * @param type $input
     * @param type $type
     * @return array
     */
    function parsejson($input, $type = 'file') {
        $configs = ($type == "file") ? json_decode(file_get_contents($input), true) : json_decode($input, true);
        return (array) $configs;
    }

    /**
     * 将指定字符串当xml解析成数组
     * @param type $input
     * @param type $type
     * @return array
     */
    function parsexml($input, $type = 'file') {
        $configs = ($type == "file") ? XmlParse::xmlLoad($input) : XmlParse::xmlLoadString($input);
        return (array) $configs;
    }

    /**
     * 将指定字符串当php解析成数组
     * @param type $input
     * @param type $type
     * @return array
     */
    function parsephp($input, $type = 'file') {
        $configs = ($type == "file") ? require $input : eval($input);
        return (array) $configs;
    }

}
