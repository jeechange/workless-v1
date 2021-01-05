<?php

namespace phpex\Library;

/**
 * Description of View
 *
 * @author river2liu
 */
class View {

    private $_assign = array();

    public function assign($key, $val = '') {
        if (is_array($key)) {
            $this->_assign = array_merge($this->_assign, $key);
            return;
        } elseif (empty($key)) {
            return;
        }
        $keys = explode(".", $key);
        $joinkey = '';
        foreach ($keys as $ckey) {
            $joinkey.="['$ckey']";
        }
        eval('$this->_assign' . $joinkey . '=$val;');
    }

    public function getAssign($key = '') {
        if (empty($key))
            return $this->_assign;
        $keys = explode(".", $key);
        $joinkey = '';
        foreach ($keys as $ckey) {
            $joinkey.="['$ckey']";
        }
        eval('$return=isset($this->_assign' . $joinkey . ')?$this->_assign' . $joinkey . ':null;');
        return $return;
    }

    public function parse($content) {
        return $content;
    }

    public function fetch($templateFile = '', $assign = null) {
        $configs = C("view");
        $basename = basename($templateFile);
        $suffix = strstr($basename, ".");
        if ($suffix)
            $templateFile = substr($templateFile, 0, -strlen($suffix));
        else {
            $suffix = "." . getkey($configs["RelateEngine"], $configs["engine"]);
        }
        $pattern = "/^(?<app>[a-zA-Z0-9_]+@)?"
                . "(?<theme>[a-zA-Z0-9_]+#)?"
                . "(?<con>[a-zA-Z0-9_]+[\\:\\/])?"
                . "(?<act>[a-zA-Z0-9_]+)?$/";
        if (!$templateFile) { // 空值
            /* @var $app AppInterface */
            $app = ins()->get("app." . ucfirst(R()->getAppName()));
            $theme = $app->getTheme() ? $app->getTheme() . "/" : "";
            $templateFile = $app->getRoot() . "/View/" . $theme . ucfirst(R()->getController()) . "/" . R()->getAction();
        } elseif (preg_match($pattern, $templateFile, $matches)) { // index
            /* @var $app AppInterface */
            if (!isset($matches["app"]) || empty($matches["app"])) {
                $appName = ucfirst(R()->getAppName());
            } else {
                $appName = ucfirst(substr($matches["app"], 0, -1));
            }
            $app = ins()->get("app." . $appName);
            if (!isset($matches["theme"]) || empty($matches["theme"])) {
                $theme = $app->getTheme() ? $app->getTheme() . "/" : "";
            } else {
                $theme = substr($matches["theme"], 0, -1) . "/";
            }
            if (!isset($matches["con"]) || empty($matches["con"])) {
                $con = R()->getController();
            } else {
                $con = substr($matches["con"], 0, -1);
            }
            $con=  ucfirst($con);
            if (!isset($matches["act"]) || empty($matches["act"])) {
                $act = R()->getAction();
            } else {
                $act = $matches["act"];
            }
            $templateFile = $app->getRoot() . "/View/" . $theme . $con . "/" . $act;
        }
        $templateFile .= $suffix;
        if (!is_file($templateFile)) {
            E("Template file does not exist:'%s'", $templateFile);
        }

        $subInfos = explode(".", $suffix);
        $subInfo = array_pop($subInfos);
        if (!isset($configs["RelateEngine"][$subInfo])) {
            E('Does not support the template engine:"%s"', $subInfo);
        }
        $EngineClass = "\\Engine\\" . $configs['RelateEngine'][$subInfo] . "Engine";

        $Engine = new $EngineClass();
        if (null === $assign) {
            $assign = $this->_assign;
        }

        $Engine->setAppName(isset($appName) ? $appName : ucfirst(R()->getAppName()));
        return $Engine->fetch($templateFile, $assign, $configs);
    }

}
