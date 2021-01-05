<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Library;

use phpex\Loader\ConfigureInterface;
use phpex\Error\ErrorHandler;
use phpex\Foundation\Response;
use phpex\Event\EventContainer;
use phpex\Console\Application;
use phpex\Foundation\Session;

/**
 *
 * @author Administrator
 */
interface MainInterface {

    public function getAppList();

    /**
     * 启动程序
     * @return boolean
     */
    public function boot();

    /**
     * @return string 日志路径
     */
    public function getLogDir();

    /**
     * 
     * @param Application $application
     */
    public function addCommands(Application $application);

    /**
     * 加载配置
     * @param ConfigureInterface $loader
     */
    public function loadConfig(ConfigureInterface $loader);

    /**
     * 执行程序
     * @return Response
     * @throws Exception
     */
    public function run();

    /**
     * 注册报错处理程序
     * @param ErrorHandler $ErrorHandler
     */
    public function registerErrorHandler(ErrorHandler $ErrorHandler);

    /**
     * 获取运行环境
     * @return string prod 或 dev 或 test
     */
    public function getEnv();

    /**
     * 获取app根路径
     * @return string
     */
    public function getAppRoot();

    /**
     * 运行时路径
     * @return string
     */
    public function getRuntime();

    /**
     * 获取vendor根路径
     * @return string
     */
    public function getVendorRoot();
    
    public function getName();
}
