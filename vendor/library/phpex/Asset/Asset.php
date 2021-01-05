<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Asset;

use phpex\Util\ORG\Image;

/**
 * Description of Asset
 *
 * @author Administrator
 */
class Asset {

    private $config = array(), $cdnConfig = array();
    private $publics = array(), $cdnPublics = array();
    private $apps = array();
    private $baseUrl = "";
    private $basePath = "";
    private $safeAccessScript = "";


    public function path($path = "") {

        if ($this->cdnConfig["enabled"]) return $this->cdnPath($path);
        $path = $this->baseUrl . str_ireplace($this->apps, $this->publics, $path);
        $realPath = str_replace("/./", "/", $path);
        do {
            if (false === ($pos = strpos($realPath, "/../"))) {
                break;
            }
            $leftPath = substr($realPath, 0, $pos);
            $rightPaht = substr($realPath, $pos + 3);
            $realPath = dirname($leftPath) . $rightPaht;
        } while ($realPath);
        return preg_replace('/\/{2,}/', '/', $realPath);
    }


    public function cdnPath($path) {
        $path = str_ireplace($this->apps, $this->cdnPublics, $path);
        $realPath = str_replace("/./", "/", $path);
        do {
            if (false === ($pos = strpos($realPath, "/../"))) {
                break;
            }
            $leftPath = substr($realPath, 0, $pos);
            $rightPaht = substr($realPath, $pos + 3);
            $realPath = dirname($leftPath) . $rightPaht;
        } while ($realPath);
        return $this->cdnConfig['url'] . preg_replace('/\/{2,}/', '/', $realPath);
    }

    public function __construct() {
        $this->cdnConfig = C("cdn") ?: array("enabled" => false);
        $this->cdnConfig["enabled"] ? $this->_cdnInit() : $this->_init();
    }

    private function _cdnInit() {
        /* @var $apps \phpex\Library\App[] */
        $apps = ins()->getTag("app");
        $i = 0;
        foreach ($apps as $app) {
            $this->apps[$i] = "[" . ucfirst($app->getName()) . "]";
            $this->cdnPublics[$i] = $app->getPublicName() . ($app->getTheme() ? "/" . $app->getTheme() : "");
            $this->cdnPublics[$i] = "/statics/" . $this->cdnConfig["name"] . "/" . $this->cdnPublics[$i];
            $i++;
        }
        $this->apps[$i + 1] = "*";
        $this->apps[$i + 2] = ",";
        $this->apps[$i + 3] = "[Core]";
        $this->cdnPublics[$i + 1] = "x";
        $this->cdnPublics[$i + 2] = "_";
        $this->cdnPublics[$i + 3] = "/libs";
    }

    private function _init() {
        $this->config = C("asset");
      //  dump( $this->config);exit;
        /* @var $apps \phpex\Library\App[] */
        $apps = ins()->getTag("app");
        $i = 0;
        foreach ($apps as $app) {
            $this->apps[$i] = "[" . ucfirst($app->getName()) . "]";
            $this->publics[$i] = $app->getPublicName() . ($app->getTheme() ? "/" . $app->getTheme() : "");
            $i++;
        }
        $this->apps[$i + 1] = "*";
        $this->apps[$i + 2] = ",";
        $this->apps[$i + 3] = "[Core]";
        $this->publics[$i + 1] = "x";
        $this->publics[$i + 2] = "_";
        $this->publics[$i + 3] = "core";
        $this->publics[$i + 2] = "_";
        $this->baseUrl = Q()->getBasePath() . "/" . $this->config["basedir"] . "/";
        $this->basePath = dirname(Q()->server->get("SCRIPT_FILENAME"));
        if (0 === stripos($this->config["safe_access_script"], "http"))
            $this->safeAccessScript = $this->config["safe_access_script"];
        else
            $this->safeAccessScript = Q()->getBasePath() . "/" . $this->config["safe_access_script"];
    }

    public function thumbnail($output, $input = "", $type = "small") {
        if ($input && is_file($input)) {
            $file = $input;
        } elseif ($input) {
            $file = $this->basePath . str_ireplace($this->apps, $this->publics, $input);
        } else {
            $file = $this->basePath . str_ireplace($this->apps, $this->publics, $output);
        }
        if (!is_file($file)) {
            $file = $this->config["_empty"]["input"];
        }
        $config = isset($this->config["thumb"][$type]) ? $this->config["thumb"][$type] : current($this->config["thumb"]);
        $output = dirname($output) . "/" . $config["prefix"] . $config["size"] . "_" . basename($output);
        $saveName = $this->basePath . str_ireplace($this->apps, $this->publics, $output);
        if (!is_file($saveName) || "dev" == main()->getEnv()) {
            list($width, $height) = explode("*", $config["size"]);
            Image::thumb($file, $saveName, $width, $height);
        }
        return $this->baseUrl . str_ireplace($this->apps, $this->publics, $output);
    }

    public function water($output, $input = "", $type = "default") {
        if ($input && is_file($input)) {
            $file = $input;
        } elseif ($input) {
            $file = $this->basePath . str_ireplace($this->apps, $this->publics, $input);
        } else {
            $file = $this->basePath . str_ireplace($this->apps, $this->publics, $output);
        }
        if (!is_file($file)) {
            $file = $this->config["_empty"]["input"];
        }
        $config = isset($this->config["water"][$type]) ? $this->config["water"][$type] : current($this->config["water"]);

        $output = dirname($output) . "/" . $config["prefix"] . "a_" . $config['alpha'] . "_p" . $config['position'] . "_m" . $config['margin'] . "_" . basename($output);
        $saveName = $this->basePath . str_ireplace($this->apps, $this->publics, $output);
        if (!is_file($saveName) || "dev" == main()->getEnv()) {
            Image::water($file, $config['path'], $saveName, $config['alpha'], $config['position'], $config['margin']);
        }
        return Q()->getBasePath() . $this->modulePath($output);
    }

    public function clip($output, $input = "", $type = "small") {
        if ($input && is_file($input)) {
            $file = $input;
        } elseif ($input) {
            $file = $this->basePath . str_ireplace($this->apps, $this->publics, $input);
        } else {
            $file = $this->basePath . str_ireplace($this->apps, $this->publics, $output);
        }
        if (!is_file($file)) {
            $file = $this->config["_empty"]["input"];
        }
        $config = isset($this->config["clip"][$type]) ? $this->config["clip"][$type] : current($this->config["clip"]);
        $output = dirname($output) . "/" . $config["prefix"] . $config['size'] . "_p" . $config['point'] . "_" . basename($output);
        $saveName = $this->basePath . str_ireplace($this->apps, $this->publics, $output);

        if (!is_file($saveName) || "dev" == main()->getEnv()) {
            list($width, $height) = explode("*", $config["size"]);
            list($x, $y) = explode(",", $config["point"]);
            Image::clip($file, $saveName, $width, $height, $x, $y);
        }
        return $this->baseUrl . str_ireplace($this->apps, $this->publics, $output);
    }

    /**
     *
     * @param type $path
     * @param type $type
     */
    public function uploadPath($path, $type) {

        if ($this->cdnConfig["enabled"]) return $this->cdnUploadPath($path, $type);

        $path = ltrim($path, "/");
        if (!isset($this->config["thumb"][$type]))
            return $this->safeAccessScript . $path;
        $basename = basename($path);
        $dirname = dirname($path);
        $thumbconfig = $this->config["thumb"][$type];
        return $this->safeAccessScript . $dirname . "/" . $thumbconfig["prefix"] . str_replace("*", "_", $thumbconfig["size"]) . "_" . $basename;
    }


    public function cdnUploadPath($path, $type) {
        $path = ltrim($path, "/");
        if (!isset($this->config["thumb"][$type])) return $this->cdnConfig['url'] . "/uploads/" . $path;

        $basename = basename($path);
        $dirname = dirname($path);
        $thumbconfig = $this->config["thumb"][$type];
        return $this->cdnConfig['url'] . "/uploads/" .  $dirname . "/" . $thumbconfig["prefix"] . str_replace("*", "_", $thumbconfig["size"]) . "_" . $basename;
    }
}
