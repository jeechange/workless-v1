<?php
namespace Consoles;

use Admin\DModel\CompanyDModel;
use phpex\Access\Access;
use phpex\Library\App;

class ConsolesApp extends App {

    public function getName() {
        return "consoles";
    }

    public function getRouteConfig() {
        $config = parent::getRouteConfig();
        $config->prefix = "";
        // $config->domain = "workless.cn";
        // $config->secure = true;
        return $config;
    }

    public function getLoginUrl() {
        $get = Q()->get->all();
        if (Q()->get->has("company") && Q()->get->has("user")) {
            $params = array("company" => $get['company'], "user" => $get['user']);
            if (Q()->get->has("code") && Q()->get->get("code")) $params["code"] = Q()->get->get("code");
            return url("consoles_login_login", $params);
        }
        return url("consoles_login_login");
    }

    public function buildAccess(Access $access) {
        $access->addIgnore("Login");
        $access->addIgnore("Layout");
        $access->addIgnore("Share");
        $access->addIgnore("Share");
        $access->addIgnore("Myuser", 'yzVcode');
        $access->addIgnore("Myuser", 'sendVerify');
        $access->addIgnore("Myuser", 'verify');
        //$access->addIgnore("Index");
        $access->loadSession();
        $access->closeNodeAccess($this->isSuper($access));
    }


    private function isSuper(Access $access) {
        return true;
        if (!$access->getUser()) return false;
        $session = Q()->getSession();
        $sid = $access->getUser("sid");
        if (!$sid) return false;
        $sessionKey = "closeNodeAccessIfSuperFor" . $sid;
        if ($session->has($sessionKey)) {
            return $session->get($sessionKey);
        }

        $companyDM = CompanyDModel::getInstance();

        $company = $companyDM->find($sid);
        if (!$company || $company->getSuperid() != $access->getUser("id")) {
            $session->set($sessionKey, false);
            $session->save();
            return false;
        }

        $session->set($sessionKey, true);
        $session->save();
        return true;
    }
}
