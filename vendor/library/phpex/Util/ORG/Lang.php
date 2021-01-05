<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Util\ORG;

/**
 * Description of Lang
 *
 * @author river2liu <river2liu@jeechange.com>
 */
class Lang {

    /**
     * @var Lang
     */
    private static $instance;
    private $var_name = "l";
    private $current_lang = "zh-cn";
    private $default_lang = "zh-cn";
    private $langs = array();

    private function __construct() {
        $lang = C("lang");
        $langdir = C("config_path") . "lang/";
        foreach ($lang as $value) {
            $this->langs[$value["names_en"]] = parseFile($langdir . $value["package"]);
        }
        $this->setVarName($this->var_name);
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public function setCurrentLang($name) {
        if (isset($this->langs[$name]))
            $this->current_lang = $name;
        else
            debug_throws("不支持此语言：%s", $name);
    }

    public function setDefaultLang($name) {
        if (isset($this->langs[$name]))
            $this->default_lang = $name;
        else
            debug_throws("不支持此语言：%s", $name);
    }

    public function getDefalutLang() {
        return $this->default_lang;
    }

    public function getCurrentLang() {
        return $this->current_lang;
    }

    public function setVarName($var_name) {
        $this->var_name = $var_name;
        if (isset($_GET[$var_name]) && isset($this->langs[$_GET[$var_name]])) {
            $this->current_lang = $_GET[$var_name];
        }
    }

    public static function translation($key) {
        $instance = self::getInstance();
        return $instance->translations($key);
    }

    public function translations($key) {
        if (isset($this->langs[$this->current_lang][$key])) {
            return $this->langs[$this->current_lang][$key];
        } elseif (isset($this->langs[$this->default_lang][$key])) {
            return $this->langs[$this->default_lang][$key];
        } else {
            return sprintf("__%s__", $key);
        }
    }

}
