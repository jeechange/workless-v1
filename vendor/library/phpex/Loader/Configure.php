<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Loader;

use phpex\Library\Container;

/**
 * Description of Configure
 *
 * @author Administrator
 */
class Configure implements ConfigureInterface {

    protected $configures = array();

    /**
     * 
     * @param type $path 加载的路径
     * @param type $reference 索引方式 null:与原有的配置合并且覆盖原有的值，true与原有的合并但不覆盖原有的， 其它:新建的个索引
     */
    public function load($path, $reference = null) {
        if (null === $reference)
            $this->configures = array_replace_recursive($this->configures, parseFile($path));
        elseif (true === $reference)
            $this->configures = array_replace_recursive(parseFile($path), $this->configures);
        else
            $this->configures[$reference] = isset($this->configures[$reference]) ?
                    array_replace_recursive($this->configures[$reference], parseFile($path)) :
                    parseFile($path);
        if (!empty($this->configures) && is_array($this->configures)) {
            $this->array_map($this->configures, $this->configures);
            if (isset($this->configures['imports'])) {
                $imports = $this->configures['imports'];
                unset($this->configures['imports']);
                foreach ($imports as $import) {
                    $import_configs = parseFile($import);
                    $this->configures = array_merge_recursive((array) $import_configs, $this->configures);
                }
            }
        }
    }

    private function array_map(&$configs, &$topconfigs) {
        if (!is_array($configs)) {
            $configs = preg_replace_callback("/%([^%#]*)#?([^%]*)%/", function($matches) {
                $name = strstr($matches[1], " ", true);
                $container = ins()->has($name) ? ins()->get($name) : ins()->get("core.config");
                $method = substr($matches[1], strlen($name) + 1);
                if (!method_exists($container, $method))
                    $method = "get";
                $args = explode(",", $matches[2]);
                $return = call_user_func_array(array(&$container, $method), $args);
                if (is_scalar($return) || is_null($return)) {
                    return $return;
                } else {
                    E("The '%s' return value must be a scalar,give(%s)", $matches[0], strtoupper(typeof($return)));
                }
            }, $configs
            );
            if (0 === strpos($configs, "@")) {
                $configs = parseFile(substr($configs, 1));
            }
        } else {
            foreach ($configs as &$val) {
                $this->array_map($val, $topconfigs);
            }
        }
    }

    /**
     * 根据索引获取配置值
     * @param string $key
     * @return mixed
     */
    public function get($key = '') {
        if (empty($key))
            return $this->configures;
        $keys = explode(".", $key);
        $joinkey = '';
        foreach ($keys as $ckey) {
            $joinkey.="['$ckey']";
        }
        $configs = $this->configures;
        $evalStr = '$return=isset($configs' . $joinkey . ')?$configs' . $joinkey . ':null;';
        eval($evalStr);
        return $return;
    }

    public function sets(array $sets) {
        $this->configures = array_replace($this->configures, $sets);
    }

    public function set($key, $value) {
        $keys = explode(".", $key);
        $joinkey = '';
        foreach ($keys as $ckey) {
            $joinkey.="['$ckey']";
        }
        $configs = $this->configures;
        $evalStr = '$configs' . $joinkey . '=$value;';
        eval($evalStr);
        $this->configures = $configs;
    }

    /**
     * 检查指定的索引值是否存在
     * @param string $key
     * @return bollean
     */
    public function has($key) {
        $keys = explode(".", $key);
        $joinkey = '';
        foreach ($keys as $ckey) {
            $joinkey.="['$ckey']";
        }
        $configs = $this->configures;
        $evalStr = '$return=isset($configs' . $joinkey . ')?true:false;';
        eval($evalStr);
        return $return;
    }

}
