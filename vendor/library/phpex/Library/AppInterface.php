<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Library;

use phpex\Foundation\Session;
use phpex\Loader\ConfigureInterface;
use phpex\Library\RouteInterface;

/**
 *
 * @author Administrator
 */
interface AppInterface {

    public function loadConfig(ConfigureInterface $Configure);

    public function loadRoute(RouteInterface $route);

    public function getRouteConfig();

    public function getRoot();

    public function getName();

    public function getPublicName();

    public function getSessionName();

    public function getTheme();

    public function run($controller, $action, array $parameters = array());

    public function initSession();

    public function installInstance(Instance $instance);

    public function getNamespace();

    public function getRunController();
}
