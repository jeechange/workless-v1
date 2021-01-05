<?php

use phpex\Library\Main;
use phpex\Loader\ConfigureInterface;

date_default_timezone_set('PRC');

class appMain extends Main {

    const VERSION = 1.0;

    const SOCKET_PORT = 80;

    /**
     * 构建应用的模块
     * @return array
     */
    public function getAppList() {
        return array("Home", "Jeechange", "Admin", "Consoles", "Node", "MobileConsoles");
    }

    /**
     * 加载配置文件
     * @param ConfigureInterface $loader
     */
    public function loadConfig(ConfigureInterface $loader) {
        $loader->load(__DIR__ . "/config/config.yml");
    }

    public function getName() {
        return "workless";
    }

    public function getRuntime() {
        return dirname(__DIR__) . "/runtime";
    }

    public function run() {
        $this->boot();
        if (substr(ltrim(Q()->getPathInfo(), "/"), 0, strlen(C("asset.prefix"))) == C("asset.prefix")) {
            return new \Jeechange\Lib\AssetResponse();
        }
        return parent::run();
    }

}
