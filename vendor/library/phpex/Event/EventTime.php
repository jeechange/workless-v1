<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Event;

/**
 * Description of EventTime
 *
 * @author Administrator
 */
class EventTime {

    private $times = array();

    public function setTime($eventName, $time) {
        $this->times[strtolower($eventName)] = intval($time);
    }

    public function getTimes() {
        return $this->times;
    }

}
