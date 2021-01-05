<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Library;

use phpex\Error\ErrorHandler;
use phpex\Foundation\Response;
use phpex\Console\Application;
use phpex\Console\CommandFind;

/**
 * Description of Build
 *
 * @author Administrator
 */
abstract class Main implements MainInterface {

    const VERSION = '1.0.0';

    protected $build = array("phpex\\Library\\Build", "buildAppList");
    private $appRootDir;
    private $native_debug = false;
    private $appLists = array();
    private $vendorRootDir;
    private $mainRootDir;
    private $environment;
    private $debug;
    private $booted = false;
    private $commands = array();
    private $sessionConfig = array();

    public function __construct($environment, $debug = false) {
        $this->environment = $environment;
        $this->debug = (bool)$debug;
        ins()->addInstance("core.main", $this);
        if ($debug === 1) {
            $this->native_debug = true;
        }
        $this->registerErrorHandler(new ErrorHandler);
    }

    /**
     * 注册报错处理程序
     * @param \phpex\Error\ErrorHandler $ErrorHandler
     */
    public function registerErrorHandler(ErrorHandler $ErrorHandler) {
        if ($this->native_debug) {
            error_reporting(E_ALL);
            return;
        }
        error_reporting(0);//在注册报错处理程序之前，先关闭系统默认的报错提示
        $ErrorHandler->register_shutdown_function(array('phpex\\Error\\phpexError', 'fatalError'));
        $ErrorHandler->set_error_handler(array('phpex\\Error\\phpexError', 'appError'));
        $ErrorHandler->set_exception_handler(array('phpex\\Error\\phpexError', 'appException'));
    }

    /**
     * 执行程序
     * @return Response
     * @throws Exception
     */
    public function run() {
        $this->boot();
        /* @var $apps App[] */
        $apps = ins()->getTag("app");
        $route = R();
        foreach ($apps as $app) {
            $app->loadRoute($route);
        }
        $response = $route->dispatch()->getResponse();
        if (is_object($response) && $response instanceof Response)
            return $response;
        E("The controller must return a response (%s given). Did you forget to add a return statement somewhere in your controller", typeof($response));
    }

    /**
     * 启动程序
     * @return boolean
     */
    public function boot() {
        if (!$this->booted) {
            ins()->newInstance("core.event", "phpex\\Event\\EventContainer");
            ins()->newInstance("core.config", "phpex\Loader\\Configure");
            $config = C();
            $config->load($this->getMainRoot() . "/Common/general.yml");
            $this->loadConfig($config);
            if ($config->has("service"))
                ins()->install($config->get("service"));
            $this->_initialize();
            call_user_func($this->build, $this);
            $this->booted = true;
        }
        return true;
    }

    protected function _initialize() {

    }

    /**
     *
     * @param Application $application
     */
    public function addCommands(Application $application) {
        $command_path = $this->getMainRoot() . "/Command";
        $commandFind = new CommandFind();
        $commandFind->find("phpex", $command_path);
        $apps = ins()->getTag("app");
        foreach ($apps as $app) {
            list($namespace) = explode("\\", get_class($app));
            $commandFind->find($namespace, $app->getRoot() . "/Command");
        }
        $application->addCommands($commandFind->getCommands());
    }

    /**
     * 获取运行环境
     * @return string prod 或 dev 或 test
     */
    public function getEnv() {
        return $this->environment;
    }

    /**
     * 检查debug是否开启
     * @return boolean
     */
    public function getDebug() {
        return $this->debug;
    }

    /**
     * 获取app根路径
     * @return string
     */
    public function getAppRoot() {
        if (null === $this->appRootDir) {
            $r = new \ReflectionObject($this);
            $this->appRootDir = str_replace('\\', '/', dirname($r->getFileName()));
        }
        return $this->appRootDir;
    }

    /**
     * 运行时路径
     * @return string
     */
    public function getRuntime() {
        return dirname($this->getVendorRoot()) . "/runtime";
    }

    /**
     * @return string 日志路径
     */
    public function getLogDir() {
        return $this->getRuntime() . "/Logs";
    }

    /**
     * 获取vendor根路径
     * @return string
     */
    public function getVendorRoot() {
        if (null === $this->vendorRootDir) {
            $ref = new \ReflectionClass("Composer\Autoload\ClassLoader");
            $this->vendorRootDir = dirname(dirname($ref->getFileName()));
        }
        return $this->vendorRootDir;
    }

    public function getMainRoot() {
        if (null === $this->mainRootDir) {
            /* @var $load \Composer\Autoload\ClassLoader */
            $load = ins()->get("core.autoload");
            $prefixes = $load->getPrefixes();
            $this->mainRootDir = str_replace('\\', '/', $prefixes["phpex\\"][0]) . "/phpex";
        }
        return $this->mainRootDir;
    }

}
