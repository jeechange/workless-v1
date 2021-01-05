<?php

namespace MobileConsoles;

use phpex\Access\Access;
use phpex\Library\App;

class MobileConsolesApp extends App {

    public function getName() {
        return "mobileConsoles";
    }

    public function getRouteConfig() {
        $config = parent::getRouteConfig();
        $config->prefix = "/MobileConsoles";
        //$config->domain = "m.console.workless";
//        $config->secure = true;
        return $config;
    }

    public function run($controller, $action, array $parameters = array()) {
        if (isset($_SERVER["HTTP_AJAX"])) {
            $_SERVER['HTTP_X_REQUESTED_WITH'] = "XMLHttpRequest";
            Q()->server->set("HTTP_X_REQUESTED_WITH", "XMLHttpRequest");
        }
        return parent::run($controller, $action, $parameters);
    }

    public function getLoginUrl() {
        $get = Q()->get->all();
        $userAgent = Q()->headers->get("user-agent");
        if (preg_match("#WindowsWechat#", $userAgent)) {
            $routeName = "consoles_index_index";
        } else {
            $routeName = "mobileConsoles_login";
        }
        if (Q()->get->has("company") && Q()->get->has("user")) {
            $params = array("company" => $get['company'], "user" => $get['user']);
            if (Q()->get->has("code") && Q()->get->get("code")) $params["code"] = Q()->get->get("code");
            return url($routeName, $params);
        }
        return url($routeName);
    }

    public function buildAccess(Access $access) {
        $access->addIgnore("Login");
        $access->addIgnore("Layout");
        $access->addIgnore("Share");
        $access->loadSession();
        $access->closeNodeAccess(true);
    }
}
