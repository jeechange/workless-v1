<?php

namespace phpex\Access;

use phpex\Foundation\Response;
use phpex\Library\View;

/**
 * Description of Access
 *
 * @author Administrator
 */
class Access {

    protected $user;
    protected $access_session_name = "access";
    protected $url;
    protected $rolename = "guest";
    protected $ignore = array();
    protected $controller, $action, $parameters;
    protected $errorTpl = "";
    protected $nodes = array();
    protected $actionNames = array();
    protected $closeNodeAccess = false;

    /**
     *
     * @var \ReflectionObject
     */
    protected $userMap;
    protected $response;

    public function __construct($controller, $action, $parameters) {
        $this->controller = $controller;
        $this->action = $action;
        $this->parameters = $parameters;
    }

    /**
     * 设置用户信息
     * @param mixed $user
     */
    public function setUser($user) {
        $this->user = $user;
        if ($user && is_object($user)) {
            $this->userMap = new \ReflectionObject($user);
        }
    }

    /**
     * 关闭节点访问控制
     * @param type $close
     */
    public function closeNodeAccess($close = true) {
        $this->closeNodeAccess = (bool)$close;
    }

    public function setLoginUrl($url) {
        $this->url = $url;
    }

    public function setAccessSessionName($accessSessionName) {
        $this->access_session_name = $accessSessionName;
    }

    public function clearAccredit() {
        Q()->getSession()->remove($this->access_session_name)->save();
    }

    public function accredit($user, $rolename = "", $confs = array()) {
        $this->setUser($user);
        $rolename or $rolename = $this->getUser("roleName");
        /* @var $app \phpex\Library\AppInterface */
        $app = ins()->get("app." . R()->getAppName());
        $confs = $confs ?: array($app->getNamespace(), $app->getRoot() . "/Conf");
        $rolenames = explode(",", $rolename);
        $weight = 0;
        $this->buildNodes($inodes, $confs[1], $confs[0]);

        $this->filterNode($rolenames, $confs[1], $inodes, $fNode, $weight);
        $accessInfo = array(
            "rolename" => $rolenames,
            "weight" => $weight,
            "user" => $user,
            "nodes" => $fNode
        );

        Q()->getSession()->set($this->access_session_name, $accessInfo)->save();
    }

    public function buildNodes(&$inodes, $basePath, $namespace, $defaultAccredit = 1, $defaultGuest = 0) {
        $inodes = parseFile($basePath . "/node.yml");
        is_array($inodes) or $inodes = array();
        if (!is_file($basePath . "/menu.yml")) {
            $navbars = array();
        } else {
            $navbars = parseFile($basePath . "/menu.yml");
            if (!is_array($navbars)) {
                $navbars = array();
            }
        }
        $namespaceLen = strlen($namespace) + 12;
        $changed = false;
        foreach ($navbars as $nav) {
            $act_info = R()->getActionForRoute($nav[2], $nav[3]);
            if (!$act_info)
                continue;
            $action = sprintf("%s.%s.%s", $act_info["app"], $act_info["controller"], $act_info["action"]);
            if ($act_info["params"]) {
                $action .= "." . str_replace(array("&", "="), array(".", ":"), http_build_query($act_info["params"]));
            }
            if (isset($inodes[$action])) {
                continue;
            }
            $changed = true;
            $inodes[$action] = array(
                "name" => $nav[1],
                "accredit" => $defaultAccredit,
                "guest" => $defaultGuest
            );
        }
        $path = realpath($basePath . "/../Controller");
        $files = glob($path . "/*Controller.php");
        foreach ($files as $file) {
            $classname = $namespace . "\\Controller\\" . rtrim(basename($file), ".php");
            $r = new \ReflectionClass($classname);
            if ($r->isSubclassOf('phpex\\Library\\Controller') && $r->isSubclassOf('phpex\\Access\\AccessInterface') && !$r->isAbstract()) {
                $names = substr($classname, $namespaceLen, -10);
                /* @var $methods \ReflectionMethod[] */
                $methods = $r->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    if ($classname != $method->class)
                        continue;
                    $doc = $method->getDocComment();
                    if (preg_match("/@ignore/", $doc)) {
                        continue;
                    }
                    $action = sprintf("%s.%s.%s", $namespace, $names, $method->getName());
                    if (isset($inodes[$action])) {
                        continue;
                    }
                    $changed = true;
                    $inodes[$action] = array(
                        "name" => $this->getNames($method->getName(), $doc),
                        "accredit" => $defaultAccredit,
                        "guest" => $defaultGuest
                    );
                }
            }
        }
        ksort($inodes);
        $changed and file_put_contents($basePath . "/node.yml", arrDump($inodes));
    }

    private function getNames($action, $doc) {
        if ($doc && preg_match("/@name\s+(\S+)/", $doc, $name)) {
            return $name[1];
        } elseif (isset($this->actionNames[$action])) {
            return $this->actionNames[$action];
        } else {
            return $action;
        }
    }

    public function filterNode(array $rolenames, $basePath, array $inodes, &$fNode, &$weight = 0) {
        $roles = parseFile($basePath . "/role.yml");
        is_array($iroles) or $iroles = array();
        foreach ($roles as $key => $role) {
            if (in_array($key, $rolenames)) {
                $weight = $weight | $role["weight"];
            }
        }
        if (!$weight) {
            $fNode = array();
            return;
        }
        foreach ($inodes as $name => $node) {
            if (!isset($node["accredit"]))
                continue;
            if ($node["accredit"] & $weight) {
                $fNode[$name] = $node;
            } elseif ($node["guest"]) {
                $fNode[$name] = $node;
            }
        }
    }

    /**
     * 获取用户信息
     * @return mixed
     */
    public function getUser($key = null) {
        if (!$key)
            return $this->user;
        if ($this->userMap && $this->userMap->hasProperty($key)) {
            $property = $this->userMap->getProperty($key);
            $property->setAccessible(true);
            return $property->getValue($this->user);
        }
        return null;
    }

    /**
     * 刷新用户的session
     * @return
     */
    public function flushUser() {
        if (!$this->user)
            return;
        $this->user = DM()->getManager()
            ->createQuery(sprintf("select u from %s u where u.id=%d", get_class($this->user), $this->user->getId()))
            ->setMaxResults(1)->getOneOrNullResult();
        $access = (array)Q()->getSession()->get($this->access_session_name);
        $access = array_merge($access, array("user" => $this->user));
        Q()->getSession()->set($this->access_session_name, $access);
        Q()->getSession()->save();
    }

    /**
     * 获取登录的url
     * @return string
     */
    public function getLoginUrl() {
        return $this->url;
    }

    public function isAccredit($controller = "", $action = "", array $parameter = array()) {
        if ($this->closeNodeAccess) {
            return true;
        }
        $controller = $controller ?: $this->controller;
        $action = $action ?: $this->action;
        $parameter = $parameter ?: $this->parameters;
        $node = sprintf("%s.%s.%s", R()->getAppName(), $controller, $action);
        ksort($parameter);
        do {
            $pstr = http_build_query($parameter);
            $pname = $pstr ? $node . "." . str_replace(array("&", "="), array(".", ":"), $pstr) : $node;
            if (isset($this->nodes[$pname])) {
                return true;
            }
            array_pop($parameter);
        } while (count($parameter) > 0);
        return isset($this->nodes[$node]);
    }

    public function isIgnore() {
        if (isset($this->ignore[R()->getAppName()][$this->controller][$this->action]))
            return true;
        elseif (isset($this->ignore[R()->getAppName()][$this->controller]["*"]))
            return true;
        return false;
    }

    public function isLogin() {
        return $this->user ? true : false;
    }

    public function addIgnore($controller, $action = "*", $app = null) {
        if (null === $app)
            $app = R()->getAppName();
        $this->ignore[$app][$controller][$action] = true;
        return $this;
    }

    public function setErrorTpl($tpl) {
        $this->errorTpl = $tpl;
    }

    public function loadSession() {
        $accessInfo = Q()->getSession()->has($this->access_session_name) ?
            Q()->getSession()->get($this->access_session_name) : array();
        if ($accessInfo) {
            isset($accessInfo["user"]) and $this->setUser($accessInfo["user"]);
            $this->rolename = isset($accessInfo["rolename"]) ? $accessInfo["rolename"] : $this->rolename;
            $this->nodes = isset($accessInfo["nodes"]) ? $accessInfo["nodes"] : $this->nodes;
        }
        if (isset($this->nodes[0]))
            unset($this->nodes[0]);
    }

    public function setResponse($response) {
        $this->response = $response;
    }

    public function getResponse() {
        if ($this->response) {
            return $this->response;
        }
        if ($this->isIgnore()) {
            return true;
        }

        if (empty($this->user))
            return $this->jumpLogin();
        if ($this->isAccredit()) {
            return true;
        }
        if (!ins()->has("core.view")) {
            $view = new View();
            ins()->addInstance("core.view", $view);
        } else {
            $view = ins()->get("core.view");
        }
        $headers = array("Content-Type" => C("response.contentType") . ";charset=" . C("response.charset"));
        return new Response($view->fetch($this->errorTpl ?: main()->getMainRoot() . "/Common/denied"), 403, $headers);
    }

    private function jumpLogin() {
        $headers = array("Content-Type" => C("response.contentType") . ";charset=" . C("response.charset"));
        $response = new Response("", 200, $headers);
        if (!$this->url) {
            $response->setContent('<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>没有权限</title>
    </head>
    <body>
        <h5>请先设置登录URL.</h5>
    </body>
</html>');
        } else {
            $response->setStatusCode(200);
            $parse = parse_url($this->url);
            if (isset($parse["query"])) {
                parse_str($parse["query"], $queryarr);
                if (!isset($queryarr['redirect_url'])) {
                    $queryarr['redirect_url'] = Q()->getUri();
                }
                $urls = explode("?", $this->url);
                $url = $urls[0] . "?" . http_build_query($queryarr);
            } else {
                $queryarr = array("redirect_url" => Q()->getUri());
                $url = $this->url . "?" . http_build_query($queryarr);
            }

            $response->setContent('<!DOCTYPE html><html>
        <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>请先登录</title></head>
        <body onload="document.getElementById(\'jumpToLogin\').click()"><a id="jumpToLogin" href="' . $url . '">正在跳转..</a></body>
</html>');
        }
        return $response;
    }

}
