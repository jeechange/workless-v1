<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Library;

/**
 * Description of RouteConfig
 *
 * @author Administrator
 */
class RouteConfig {

    /**
     * 路由前缀
     * @var string 
     */
    public $prefix = "";

    /**
     * 路由域名限制
     * @var string 
     */
    public $domain = "";

    /**
     * 路由加密方式，写类名
     * @var string
     */
    public $encrypt = "";

    /**
     * 路由默认路径
     * @var string 
     */
    public $path = "";

    /**
     * 是否使用https
     * @var boolean|null
     */
    public $secure = null;

    /**
     *
     * @var integer 
     */
    public $route_mode;

}
