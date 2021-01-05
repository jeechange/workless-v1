<?php

namespace phpex\Library;

use phpex\Loader\ConfigureInterface;
use phpex\Library\RouteInterface;
use phpex\Foundation\Session;
use phpex\Access\Access;
use phpex\Foundation\Response;

abstract class App implements AppInterface {

    protected $sessionName;
    protected $root;
    protected $namespace;

    /**
     * @var \ReflectionObject
     */
    protected $reflection;
    protected $runController;

    public function getRoot() {
        if (null === $this->root) {
            $this->root = dirname($this->reflection->getFileName());
        }
        return $this->root;
    }

    public function __construct() {
        $this->reflection = new \ReflectionObject($this);
    }

    public function getRouteConfig() {
        return new RouteConfig();
    }

    public function loadRoute(RouteInterface $route) {
        $route->loadRoute($this->getRoot() . "/Conf/route.yml", $this->getRouteConfig());
    }

    public function loadConfig(ConfigureInterface $Configure) {
        $Configure->load($this->getRoot() . "/Conf/config.yml", $this->getName());
    }

    public function installInstance(Instance $instance) {

    }

    public function getPublicName() {
        return $this->getName();
    }

    public function getSessionName() {
        if (null === $this->sessionName) {
            $this->sessionName = main()->getName() . "_" . $this->getName() . "_phpid";
        }
        return $this->sessionName;
    }

    public function getTheme() {
        return "default";
    }

    public function initSession($config = array()) {
        if (Q()->getSession()) {
            return;
        }
        $sessionConfig = array(
            "session_auto_start" => false,
            "session_name" => $this->getSessionName(),
            "cookie_domain" => NULL,
            "session_path" => NULL,
            "session_callback" => NULL,
        );
        $session = new Session(array_merge($sessionConfig, $config));

        if (Q()->get->has($this->getSessionName())) {
            $session->session_id(Q()->get->get($this->getSessionName()));
        } elseif (Q()->post->has($this->getSessionName())) {
            $session->session_id(Q()->post->get($this->getSessionName()));
        } elseif (Q()->cookies->has($this->getSessionName())) {
            $session->session_id(Q()->cookies->get($this->getSessionName()));
        }
        $timeout = isset($config["session_timeout"]) ? intval($config["session_timeout"]) : 0;
        if ($timeout) {
            session_set_cookie_params($timeout,"/");
            ini_set("session.gc_maxlifetime", $timeout);
            $session_id = $session->session_id();
            $session_file = $session->path() . "/sess_" . $session_id;
            if ($session_id && is_file($session_file) && filemtime($session_file) < time() - $timeout) {
                @unlink($session_file);
            }
        }
        if (!$session->isStarted())
            $session->start();
        Q()->setSession($session);
    }

    public function run($controller, $action, array $parameters = array()) {
        $this->initSession(C("session"));
        $classname = $this->getNamespace() . "\\Controller\\{$controller}Controller";
        try {
            $ref = new \ReflectionClass($classname);
        } catch (\Exception $ex) {
            E("The controller does not exist for '%s',message:'%s'", $controller, $ex->getMessage());
        }
        if (!$ref->isInstantiable()) {
            E("The controller does not instantiable for '%s'", $classname);
        }
        $object = $ref->newInstance();
        $access = new Access($controller, $action, $parameters);
        $access->setLoginUrl($this->getLoginUrl());
        $this->buildAccess($access);
        $object->setAccess($access);
        if ($ref->isSubclassOf("phpex\\Access\\AccessInterface")) {
            $response = $access->getResponse();
            if ($response instanceof Response)
                return $response;
        }
        $refo = new \ReflectionObject($object);
        if (!$refo->hasMethod($action)) {
            E("The requested method does not exist: %s::%s", $classname, $action);
        }
        $method = $refo->getMethod($action);
        if (!$method->isPublic() || $method->isStatic()) {
            throws("The requested method does not access: %s::%s ", $method->getFileName(), $method->getStartLine(), "", $classname, $action);
        }
        $methodParameters = $method->getParameters();
        $pargs = array();
        foreach ($methodParameters as $index => $parameter) {
            $name = $parameter->getName();
            if (isset($parameters[$name]))
                $pargs[$index] = $parameters[$name];
            elseif ($parameter->isDefaultValueAvailable())
                $pargs[$index] = $parameter->getDefaultValue();
            else
                E("Please pass in routing parameters: %s::%s for %s,see route to '%s'", $classname, $action, $name, R()->getRunRoute());
        }

        if ($ref->hasProperty("access")) {
            $accessProperty = $ref->getProperty("access");
            $accessProperty->setAccessible(true);
            $accessProperty->setValue($object, $access);
        }
        $this->runController = $object;
        unset($methodParameters, $ref, $refo);
        return $method->invokeArgs($object, $pargs);
    }

    public function getLoginUrl() {

    }

    protected function buildAccess(Access $access) {

    }

    public function isAutoBuild() {
        return true;
    }

    public function getNamespace() {
        if (null === $this->namespace) {
            $this->namespace = $this->reflection->getNamespaceName();
        }
        return $this->namespace;
    }

    public function getRunController() {
        return $this->runController;
    }

}
