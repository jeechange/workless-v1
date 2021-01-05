<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Event;

/**
 * Description of Eventcontainer
 *
 * @author Administrator
 */
class EventContainer {
    /* @var $event EventEmitter */

    private $event;

    public function __construct() {
        $this->event = new EventEmitter();
    }

    /**
     * 注册事件
     * @param string $event 事件名
     * @param callback $callback 回调方法
     * @param integer $time 触发次数，0表示无限次数 
     * @return boolean
     */
    public function register($event, $callback, $time = 0) {
        if (!is_callable($callback) || !is_int($time))
            return FALSE;
        if (0 == $time) {
            $this->event->on($event, $callback);
            return true;
        } elseif (1 == $time) {
            $this->event->once($event, $callback);
            return true;
        } else {
            $this->event->many($event, $time, $callback);
            return true;
        }
        return false;
    }

    /**
     * 注销指定事件
     * @param string $event
     * @param callback $callback
     */
    public function off($event, $callback = null) {
        $this->event->off($event, $callback);
    }

    /**
     * 注销所有事件
     * @param string $event
     */
    public function offAll($event = null) {
        $this->event->removeAllListeners($event);
    }

    /**
     * 
     * @param string $event
     * @param type $args
     * @return type
     */
    public function trigger($event, $args = null) {
        $args = (null === $args) ? array($event) : func_get_args();
        return call_user_func_array(array(&$this->event, "emit"), $args);
    }

    /**
     * 获取某个事件执行的结果
     * @param string $event 指定的事件，留空则返回所有事件的结果
     * @return type
     */
    public function getResult($event = null) {
        return $this->event->getResult($event);
    }

    /**
     * 从指定的文件夹中加载并注册事件
     * @param string $namespace 所对应的命名空间
     * @param string $path  所对应的路径
     */
    public function load($namespace, $path) {
        $files = glob($path . "/*Event.php");       
        if ($files) {
            foreach ($files as $file) {
                $classname = $namespace . "\\Listener\\Event\\" . rtrim(basename($file), ".php");
                $r = new \ReflectionClass($classname);
                if ($r->isSubclassOf('phpex\\Event\\Event') && !$r->isAbstract()) {
                    $r->newInstance($this);
                }
            }
        }
    }

}
