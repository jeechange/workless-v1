<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2016/12/19
 * Time: 9:56
 */

namespace phpex\Cache;


class FileCache implements ICache {

    protected $path = "";


    public function __construct() {
        $this->path = main()->getRuntime() . "/data";
    }

    public function get($key) {
        $path = $this->path . "/" . $key . ".php";
        if (!is_file($path)) {
            return null;
        }
        $values = include $path;

        if (!is_array($values)) {
            return null;
        }

        if ($values["timeout"] == 0) {
            return $values["value"];
        }

        if ((time() - $values["addTime"]) > $values["timeout"]) {
            return null;
        }
        return $values["value"];
    }

    public function set($key, CacheEntity $cacheEntity) {
        $path = $this->path . "/" . $key . ".php";
        $values = array(
            "addTime" => time(),
            "value" => $cacheEntity->value,
            "timeout" => $cacheEntity->timeout
        );
        $cahceStr = "<?php return " . var_export($values, true) . ";\n";

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 777, true);
        }
        file_put_contents($path, $cahceStr);
        return true;
    }

    public function clear() {
        $files = glob($this->path . "/*.php");
        foreach ($files as $file) {
            unlink($file);
        }
    }

    public function remove($key) {
        $path = $this->path . "/" . $key . ".php";
        if (!is_file($path)) {
            return false;
        }
        unlink($path);
        return true;
    }

    public function isValid($key) {
        $path = $this->path . "/" . $key . ".php";
        if (!is_file($path)) {
            return false;
        }

        $values = include $path;

        if (!is_array($values)) return false;
        if ($values["timeout"] == 0) return true;
        if ((time() - $values["addTime"]) > $values["timeout"]) return false;
        return true;
    }
}
