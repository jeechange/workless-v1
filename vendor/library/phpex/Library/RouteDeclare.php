<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2017/6/8
 * Time: 16:25
 */

namespace phpex\Library;


class RouteDeclare {

    private $name, $method, $configs = array(
        "prefix" => "",
        "domain" => "",
        "encrypt" => null,
        "root" => "",
        "secure" => null,
        'route_mode' => Router::ROUTE_PATHINFO
    );

    private $callback, $url, $urlRequire = array(), $view;

    public function __construct($name, $method, array $configs) {
        $this->name = $name;
        $this->method = $method;
        $this->configs = $configs;
    }

    public function call($callback) {
        $this->callback = $callback;
        return $this;
    }

    /**
     * @param $url
     * @param $_
     * @return $this
     */
    public function url($url, $_ = null) {
        $args = func_get_args();
        $url = array_shift($args);
        $this->url = $url;
        $this->urlRequire = $args;
        return $this;
    }

    public function view($view) {
        $this->view = $view;
        return $this;
    }

    public function setPrefix($prefix) {
        $this->configs['prefix'] = $prefix;
        return $this;
    }

    public function setDomain($domain) {
        $this->configs['domain'] = $domain;
        return $this;
    }

    public function setEncrypt($encrypt) {
        $this->configs['encrypt'] = $encrypt;
        return $this;
    }

    public function setSecure($secure) {
        $this->configs['secure'] = $secure;
        return $this;
    }

    public function setRouteMode($routeMode) {
        $this->configs['route_mode'] = $routeMode;
        return $this;
    }

}
