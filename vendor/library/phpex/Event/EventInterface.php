<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Event;

/**
 *
 * @author Administrator
 */
interface EventInterface {

    /**
     * @return string
     */
    public function getName();

    /**
     * @return array[] Description
     */
    public function setTimes(EventTime $eventTime);

    /**
     * @param $container EventContainer
     */
    public function __construct(EventContainer $container);
}
