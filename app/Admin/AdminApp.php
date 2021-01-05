<?php
namespace Admin;
use phpex\Library\App;
class AdminApp extends App {
            
    public function getName() {
        return "admin";
    }

    public function getRouteConfig() {
        $config = parent::getRouteConfig();
        $config->prefix = "/admin";
        // $config->domain = "mobileConsoles";
//        $config->secure = true;
        return $config;
    }
}