<?php

namespace phpex\Event;

abstract class Event implements EventInterface {

    protected $eventTime;
    protected $container;
    private $basename;

    public function __construct(EventContainer $container) {
        $this->eventTime = new EventTime();
        $this->container = $container;
        $this->_initialize();
        $this->registerEvent($container);
    }

    protected function _initialize() {
        
    }

    private function registerEvent(EventContainer $container) {
        $this->setTimes($this->eventTime);
        $setTimes = $this->eventTime->getTimes();
        $rf = new \ReflectionObject($this);
        $methods = $rf->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $name = $method->getName();
            if ("on" == substr($name, 0, 2))
                $event = strtolower(substr($name, 2));
            else
                continue;
            $time = isset($setTimes[$event]) ? $setTimes[$event] : 0;
            $callback = array(&$this, $name);
            $container->register($this->getBaseame() . $event, $callback, $time);
        }
    }

    private function getBaseame() {
        if (null === $this->basename) {
            $basename = $this->getName()? : get_class($this);
            $basenameResolve = explode("\\", $basename);
            $basename = end($basenameResolve);
            $this->basename = strtolower(substr($basename,0,-5)) . ".";
        }     
        return $this->basename;
    }

}
