<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\DModel;

use Exception;

/**
 * 
 * @author Administrator
 */
class DModelInstance {

    public $defaultNamespace = "Admin";
    private $instances = array();

    public function __call($name, $arguments) {
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }
        if (strpos("_", $name))
            list($DModel, $namespace) = explode("_", $name, 2);
        else {
            $namespace = &$this->defaultNamespace;
            $DModel = &$name;
        }
        try {
            $ref = new \ReflectionClass($namespace . "\\DModel\\" . $DModel . "DModel");
            $this->instances[$name] = $ref->newInstanceArgs($arguments);
        } catch (Exception $ex) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            throws($ex->getMessage(), $trace[0]["file"], $trace[0]["line"], $trace[1]["class"] . "::" . $trace[1]["function"]);
        }
        return $this->instances[$name];
    }

}
