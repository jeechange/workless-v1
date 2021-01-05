<?php

namespace phpex\Event;

if (function_exists('FNMATCH')) {
    define('FNMATCH', true);
} else {
    define('FNMATCH', false);
}

class EventEmitter {

    /**
     * @var array Events listeners
     */
    protected $listeners = array();
    protected $result = array();
    protected $lastevent;

    /**
     * fire event
     *
     * @static
     * @param string $event
     * @param mixed  $args
     * @return bool
     */
    public function emit($event, $args = null) {
        $event = strtolower($event);
        $args = (null === $args) ? array() : array_slice(func_get_args(), 1);
        $all_listeners = array();

        foreach ($this->listeners as $name => $listeners) {
            if (strpos($name, '*') === false || !self::match($name, $event)) {
                continue;
            }

            foreach ($this->listeners[$name] as &$listener) {
                $all_listeners[$name][] = & $listener;
            }
        }

        if (!empty($this->listeners[$event])) {
            foreach ($this->listeners[$event] as &$listener) {
                $all_listeners[$event][] = & $listener;
            }
        }

        $emitted = false;
        // Loop listeners for callback
        foreach ($all_listeners as $name => $listeners) {
            $this_args = $args;
            if (strpos($name, '*') !== false) {
                array_push($this_args, $event);
            }

            foreach ($listeners as &$listener) {
                if (is_callable($listener)) {
                    // Closure Listener
                    $this->result[$event][$name] = call_user_func_array($listener, $this_args);
                    $emitted = true;
                } elseif (is_array($listener) && is_callable($listener[0])) {
                    if ($listener[1]['times'] > 0) {
                        // Closure Listener
                        $this->result[$event][$name] = call_user_func_array($listener[0], $this_args);
                        $emitted = true;
                        $listener[1]['times'] --;
                    }
                }
            }
        }
        $this->lastevent = $event;
        $result = isset($this->result[$event]) ? $this->result[$event] : null;
        $eventResolve = explode(".", $event);
        if (1 == count($eventResolve)) {
            $eventname = $event;
        } else {
            $count = count($eventResolve);
            if (is_numeric($eventResolve[$count - 1])) {
                $eventResolve[$count - 1] = "*";
            }
            $eventname = join(".", $eventResolve);
        }       
        if (is_array($result) && 1 == count($result) && isset($result[$eventname])) {
            $this->result[$event] = $result[$eventname];
        }
        return $emitted;
    }

    public function getResult($event = null) {
        if (true === $event)
            return $this->result;
        if (null === $event) {
            $event = $this->lastevent;
        }
        return isset($this->result[$event]) ? $this->result[$event] : null;
    }

    /**
     * Attach a event listener
     *
     * @static
     * @param array|string $event
     * @param callback  $listener
     * @return $this
     */
    public function on($event, $listener) {
        if (!is_callable($listener))
            return $this;
        foreach ((array) $event as $e) {
            $this->listeners[strtolower($e)][] = $listener;
        }
        return $this;
    }

    /**
     * Attach a listener to emit once
     *
     * @param array|string $event
     * @param callable     $listener
     * @return $this
     */
    public function once($event, $listener) {
        if (!is_callable($listener))
            return $this;
        foreach ((array) $event as $e) {
            $this->listeners[strtolower($e)][] = array($listener, array('times' => 1));
        }
        return $this;
    }

    /**
     * Attach a listener to emit many times
     *
     * @param array|string $event
     * @param int          $times
     * @param callable     $listener
     * @return $this
     */
    public function many($event, $times = 1, $listener) {
        if (!is_callable($listener))
            return $this;
        foreach ((array) $event as $e) {
            $this->listeners[strtolower($e)][] = array($listener, array('times' => $times));
        }
        return $this;
    }

    /**
     * Alias for removeListener
     *
     * @param array|string $event
     * @param callable|null     $listener
     * @return $this
     */
    public function off($event, $listener = null) {

        foreach ((array) $event as $e) {
            $e = strtolower($e);
            if (!empty($this->listeners[$e])) {
                if (!is_callable($listener)) {
                    unset($this->listeners[$e]);
                    continue;
                }
                if (($key = array_search($listener, $this->listeners[$e])) !== false) {
                    unset($this->listeners[$e][$key]);
                }
            }
        }
        return $this;
    }

    /**
     * Get listeners of given event
     *
     * @param string $event
     * @return array
     */
    public function listeners($event) {
        if (!empty($this->listeners[$event])) {
            return $this->listeners[$event];
        }
        return array();
    }

    /**
     * Attach a event listener
     *
     * @static
     * @param array|string $event
     * @param callback  $listener
     * @return $this
     */
    public function addListener($event, $listener) {
        if (!is_callable($listener))
            return $this;
        return $this->on($event, $listener);
    }

    /**
     * Detach a event listener
     *
     * @static
     * @param string   $event
     * @param $listener
     * @return $this
     */
    public function removeListener($event, $listener) {
        if (!is_callable($listener))
            return $this;
        return $this->off($event, $listener);
    }

    /**
     * Remove all listeners of given event
     *
     * @param string $event
     * @return $this
     */
    public function removeAllListeners($event = null) {
        if ($event === null) {
            $this->listeners = array();
        } else if (($event = strtolower($event)) && !empty($this->listeners[$event])) {
            $this->listeners[$event] = array();
        }        
        return $this;
    }

    /**
     * Match the pattern
     *
     * @param string $pattern
     * @param string $string
     * @return bool|int
     */
    protected static function match($pattern, $string) {
        if (FNMATCH) {
            return fnmatch($pattern, $string);
        } else {
            return preg_match("#^" . strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.')) . "$#i", $string);
        }
    }

}
