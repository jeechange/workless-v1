<?php

namespace Home;

use phpex\Access\Access;
use phpex\Library\App;

class HomeApp extends App {

    public function getName() {
        return "home";
    }

    public function getRouteConfig() {
        $config = parent::getRouteConfig();
        $config->prefix = "/home";
        // $config->domain = "mobileConsoles";
//        $config->secure = true;
        return $config;
    }


    public function buildAccess(Access $access) {

        $access->addIgnore("Login");
        $access->addIgnore("Layout");
        $access->addIgnore("Notify");
        $access->addIgnore("Index");
        $access->loadSession();
        $access->closeNodeAccess(true);
    }
}