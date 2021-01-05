<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Library;

/**
 * 实例管理器
 * @author Administrator
 */
class Instance {

    private $instances = array();
    private $tags = array();
    private static $instance;

    private function __construct() {
        
    }

    /**
     * @return Instance
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 构建一个对象实例
     * @param string $name 实例索引名
     * @param string $class 实例所属类
     * @param array $parameters 实例化时的参数
     * @param array $tags 索引标签
     * @return Instance
     */
    public function newInstance($name, $class, array $parameters = array(), array $tags = array()) {
        if (isset($this->instances[$name])) {
            E(sprintf("Add instance is not allowed to repeat:%s", $name));
        }

        class_exists($class) or E(sprintf("Class not found:%s", $class));
        $refClass = new \ReflectionClass($class);
        $this->instances[$name] = $refClass->newInstanceArgs($parameters);

        foreach ($tags as $tag) {
            if ($this->tags[$tag] && in_array($this->instances[$name], $this->tags[$tag]))
                continue;
            $this->tags[$tag][] = $this->instances[$name];
        }
        return $this;
    }

    /**
     * 把对象实例添加进来
     * @param string $name
     * @param object $object
     * @param array $tags
     * @return Instance
     */
    public function addInstance($name, $object, array $tags = array()) {
        if (isset($this->instances[$name])) {
            E(sprintf("Add instance is not allowed to repeat:%s", $name));
        }
        if (!is_object($object) || is_null($object))
            E(sprintf("Must for an object instance:%s", $name));
        $this->instances[$name] = $object;
        foreach ($tags as $tag) {
            if (isset($this->tags[$tag]) && in_array($object, $this->tags[$tag]))
                continue;
            $this->tags[$tag][] = $object;
        }
        return $this;
    }

    /**
     * 为实例添加一个标签
     * @param string $name
     * @param string $tag
     */
    public function addTags($name, $tag) {
        if (!isset($this->instances[$name])) {
            E(sprintf("There is no instance:%s", $name));
        }
        if ($this->tags[$tag] && in_array($this->instances[$name], $this->tags[$tag]))
            return $this;
        $this->tags[$tag][] = $this->instances[$name];
        return $this;
    }

    /**
     * 获取实例
     * @param string $name
     * @return object
     */
    public function get($name) {
        if (!isset($this->instances[$name])) {
            E(sprintf("There is no instance:%s", $name));
        }
        return $this->instances[$name];
    }

    /**
     * 返回指定标签的所有实例
     * @param string $tag
     */
    public function getTag($tag) {
        return isset($this->tags[$tag]) ? $this->tags[$tag] : array();
    }

    public function install($InstanceList) {
        
    }

    public function has($name) {
        return isset($this->instances[$name]);
    }

}
