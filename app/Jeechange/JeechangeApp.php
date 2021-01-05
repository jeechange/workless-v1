<?php

namespace Jeechange;

use phpex\Library\App;

class JeechangeApp extends App {

    public function getName() {
        return "jeechange";
    }

    public function getRouteConfig() {
        $config = parent::getRouteConfig();
        $config->prefix = "/jeechange";
        return $config;
    }


    public function getTheme() {

        return "";
    }
}