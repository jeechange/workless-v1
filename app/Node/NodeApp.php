<?php

namespace Node;

use phpex\Library\App;

class NodeApp extends App {
            
    public function getName() {
        return "node";
    }

    public function getRouteConfig() {
        $config = parent::getRouteConfig();
        $config->prefix = "/node";
        $config->secure = true;
        return $config;
    }
}
