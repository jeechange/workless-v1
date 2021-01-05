<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Library;

use phpex\Foundation\Request;

/**
 *
 * @author river2liu
 */
interface RouteInterface {

    public static function getInstance();

    public function setCacheDir($cacheDir = "");

    public function loadCache($path = "");

    public function loadRoute($path = "", RouteConfig $couteConfig = null);

    public function add($name, $pattern, $callback, $method = "any", $extra = array());

    public function setRequest(Request $request);

    public function dispatch($pathinfo = "");

    public function getController();

    public function getAction();

    public function getParameter();

    public function getResponse();
}
