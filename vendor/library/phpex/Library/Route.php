<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Library;

use phpex\Foundation\Request;
use phpex\Error\phpexError;

/**
 * Description of Route
 *
 * @author Administrator
 */
class Route implements RouteInterface {

    const ROUTE_NORMAL = 1;
    const ROUTE_PATHINFO = 2;
    const ROUTE_REWRITE = 4;

    private $methods = array(
        "GET" => 1,
        "POST" => 2,
        "PUT" => 4,
        "DELETE" => 8,
        "HEAD" => 16,
    );
    private static $instance;

    /**
     *
     * @var array
     */
    private $modePaths = array();

    /**
     * 请求类
     * @var Request
     */
    private $request;

    /**
     * 配置
     * @var array
     */
    private $options = array(
        "cacheDir" => "RouteCache",
        "var_name" => "r",
        "encrypt" => "Encryption",
        "key" => "phpex",
        'route_mode' => Route::ROUTE_PATHINFO
    );
    private $App, $Controller, $Action, $Parameter, $RunRoute;
    private $routeOptions = array(
        'prefix' => '',
        'domain' => "",
        'encrypt' => '',
        'path' => '',
        'secure' => 0,
        'route_mode' => Route::ROUTE_PATHINFO,
        'require' => array(),
        'default' => array(),
    );
    private $defaultParameter = array();

    /**
     *
     * @var Response;
     */
    private $Response;
    private $lists = array();
    private $pathInfo;

    private function __construct(Request $request = null, $cacheDir = "", $var_name = "r", $encrypt = "Encryption", $key = "phpex") {
        $this->request = $request;
        $cacheDir and $this->options['cacheDir'] = $cacheDir;
        $this->options['var_name'] = $var_name;
        $this->options['encrypt'] = $encrypt;
        $this->options['key'] = $key;
        $this->options['route_mode'] = C('route.url_mode');
        $script_name = $this->request->server->get("SCRIPT_NAME");

        $script_name = $this->trimdir($script_name);
        $this->modePaths[Route::ROUTE_NORMAL] =
        $this->modePaths[Route::ROUTE_PATHINFO] = $script_name;
        $this->modePaths[Route::ROUTE_REWRITE] = $this->dir_name($script_name);
    }

    private function trimdir($dir_name) {
        return preg_replace("#[\\\\/]{2,}#", "/", $dir_name);
    }

    private function dir_name($path) {
        $basename = basename($path);
        return $this->trimdir(substr($path, 0, 0 - strlen($basename)));
    }

    /**
     *
     * @return Route
     */
    public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new static(Q(), C("route.cache"), C("route.var_name"), C("route.encrypt"), C("route.encrypt_key"));
        }
        return self::$instance;
    }

    public function add($name, $pattern, $call, $method = "any", $extra = array()) {
        $methods = 0;
        if (empty($method) || $method == "any") {
            $methods = array_sum($this->methods);
        } else {
            $eMethod = explode("|", strtoupper($method));
            foreach ($eMethod as $m) {
                $methods = $methods | $this->methods[$m];
            }
            unset($eMethod);
        }
        $params = array();
        $callback = function ($match) use (&$params) {
            $params[$match[1]] = "";
            return "(?<{$match[1]}>[a-zA-Z0-9_]+)";
        };
        $match = preg_replace_callback("/\:([a-zA-Z0-9_]+)/", $callback, rtrim($extra["prefix"] . $pattern, "/"));
        $this->lists[$name] = array(
            "pattern" => $extra["prefix"] . $pattern,
            "match" => "/^" . str_replace("/", "\/", $match) . "$/",
            "method" => $methods,
            "callback" => $call,
            "params" => $params,
            "extra" => $extra
        );
    }

    /**
     *
     * @param string $pathInfo
     * @param string|bool $m
     * @return Route
     */
    public function dispatch($pathInfo = "", $m = false) {
        if ($pathInfo) {
            $this->pathInfo = $pathInfo;
        } else {
            $pathInfo = $this->pathInfo = $this->request->getPathInfo() ?: $this->request->get->get($this->options["var_name"]);
        }
        $method = $m === false ? $this->methods[$this->request->getMethod()] : $m;
        $callBack = array();
        $paramscount = -1;
        $lists = $this->lists;
        uksort($this->lists, function ($a, $b) use ($lists) {
            return (strlen($lists[$a]['pattern']) < strlen($lists[$b]['pattern']));
        });
        unset($lists);
        foreach ($this->lists as $name => $route) {
            if ($route["extra"]["domain"] && $route["extra"]["domain"] != $this->request->getHttpHost()) {
                //域名筛选
                continue;
            }
            if ($route["extra"]["secure"] !== null && $route["extra"]["secure"] != $this->request->isSecure()) {
                //http,https筛选
                if ($route["extra"] !== 0)
                    continue;
            }
            if (!($route["method"] & $method)) {
                //请求方式筛选
                continue;
            }
            $path = $pathInfo;
            if ($route["extra"]["encrypt"]) {
                $path = $this->deEncrypt($route["extra"]["encrypt"]);
            }
            //dump($route);
            if (preg_match($route["match"], rtrim($path, "/"), $match)) {//匹配路由
                $params = array();
                foreach ($route["params"] as $key => $param) {//填充参数
                    if (isset($match[$key])) {
                        $params[$key] = $match[$key];
                    } elseif (isset($route["extra"]["default"][$key])) {
                        $params[$key] = $route["extra"]["default"][$key];
                    }
                }
                $hasrequire = true;
                foreach ($route["extra"]["require"] ?: array() as $require => $requireMatch) {//检验参数
                    if (!isset($params[$require]) || !preg_match("/^{$requireMatch}$/", $params[$require])) {
                        $hasrequire = false;
                        break;
                    }
                }
                if (!$hasrequire)
                    continue;
                if (count($params) == 0) { //如果0个路由参数
                    $callBack = array(
                        "name" => $name,
                        "callback" => $route["callback"],
                        "params" => $params,
                    );
                    break;
                } elseif ($paramscount == -1) {//如果为第一个被匹配到的路由
                    $callBack = array(
                        "name" => $name,
                        "callback" => $route["callback"],
                        "params" => $params,
                    );
                    $paramscount = count($params);
                } elseif (count($params) < $paramscount) {//如果路由参数比之前匹配到的参数少
                    $callBack = array(
                        "name" => $name,
                        "callback" => $route["callback"],
                        "params" => $params,
                    );
                    $paramscount = count($params);
                }
            }
        }
        if (empty($callBack)) {
            phpexError::$httpStatus = 404;
            E("Not found the callback for '%s'", $pathInfo);
        }
        if ($callBack["params"] && (FALSE !== strpos($callBack["callback"], "{"))) {
            $finds = $replaces = array();
            foreach ($callBack["params"] as $find => $replace) {
                $finds[] = "{{$find}}";
                $replaces[] = $replace;
            }
            $callBack["callback"] = str_replace($finds, $replaces, $callBack["callback"]);
        }
        list($this->App, $this->Controller, $this->Action) = explode(":", $callBack["callback"]);
        /* @var $app App */
        $app = ins()->get("app." . ucfirst($this->App));
        $this->Parameter = $callBack["params"];
        $this->RunRoute = $callBack['name'];
        $this->Response = $app->run(ucfirst($this->Controller), $this->Action, $callBack["params"]);
        unset($callBack);
        return $this;
    }

    private function deEncrypt($encrypt) {
        // @todo 1 路由解密程度未写
        return $encrypt;
    }

    public function getMethods() {
        return $this->methods;
    }

    private function enEncrypt($input, $encrypt) {
        // @todo 1 路由加密程序未写
        return $input;
    }

    public function getAction() {
        return $this->Action;
    }

    public function getAppName() {
        return $this->App;
    }

    public function getController() {
        return $this->Controller;
    }

    public function getParameter() {
        return $this->Parameter;
    }

    public function getDefaultParameter() {
        return $this->defaultParameter;
    }

    public function setDefaultParameter(array $parameters, $cover = false) {
        if ($cover) {
            $this->defaultParameter = $parameters;
        } else {
            $this->defaultParameter = array_merge($this->defaultParameter, $parameters);
        }
        return $this;
    }

    public function getResponse() {
        return $this->Response;
    }

    public function getRunRoute() {
        return $this->RunRoute;
    }

    public function loadCache($path = "") {
        $lists = include $path;
        $this->lists = array_merge($this->lists, (array)$lists);
    }

    /**
     * 加载路由配置信息
     * @param string $path 配置文件路径
     * @param RouteConfig $RouteConfig 额外的配置信息
     * @return type
     */
    public function loadRoute($path = "", RouteConfig $RouteConfig = null) {
        static $first = false;
        $RouteConfig->secure === null and $RouteConfig->secure = C('route.secure');
        $routePath = $this->options["cacheDir"] . "/_" . main()->getEnv() . ".php";
        if (!is_dir(dirname($routePath))) {
            mkdir(dirname($routePath), 0777, true) or E("Unable to open or create cacheDir '%s'", dirname($routePath));
        }
        $handle = fopen($routePath, "c+");
        if (!$handle) {
            E("Unable to open or create file '%s'", $routePath);
        }
        flock($handle, LOCK_SH);
        $stat = fstat($handle);
        if ($stat["size"] > 0 && main()->getEnv() == "prod" && !$first) {
            if (!$this->lists) {
                $this->lists = include $routePath;
            }
            return;
        }
        if (!$first && $stat["size"] > 0) {
            $this->lists = (array)include $routePath;
        }
        $first = true;
        //缓存日志
        $cacheLogsPath = $this->options["cacheDir"] . "/_cache_" . main()->getEnv() . ".php";
        $cacheHandle = fopen($cacheLogsPath, "c+");
        if (!$cacheHandle) {
            E("Unable to open or create file '%s'", $cacheHandle);
        }
        //缓存键
        $cacheKey = ltrim(preg_replace("/[\:\/\\\\\.]+/", "-", $path), "-");

        flock($cacheHandle, LOCK_SH);
        $cacheStat = fstat($cacheHandle);
        $cacheLogs = $cacheStat["size"] == 0 ? array() : (array)include $cacheLogsPath;

        if ($stat["size"] == 0 || $cacheStat["size"] == 0 || !isset($cacheLogs[$cacheKey]) || $cacheLogs[$cacheKey] !== filemtime($path)) {
            $cacheLogs[$cacheKey] = filemtime($path);
            ftruncate($handle, 0);
            flock($handle, LOCK_EX | LOCK_NB);
            //添加路由配置            
            $configs = parseFile($path);
            if (!is_array($configs)) {
                E("Parse file failure '%s'", $path);
            }
            $extra = (array)$RouteConfig;

            $extra["route_mode"] or $extra["route_mode"] = $this->options['route_mode'];
            foreach ($configs as $name => $config) {
                $this->add($name, $config["pattern"], $config["callback"], $config["method"], array_replace($this->routeOptions, $extra, isset($config["options"]) ? $config["options"] : array()));
            }
            $code = sprintf("<?php\n//route cache \nreturn %s;", var_export($this->lists, true));
            if (fwrite($handle, $code, strlen($code)) !== strlen($code)) {
                E("Unable to create file '%s'", $routePath);
            }

            //缓存日志记录
            ftruncate($cacheHandle, 0);
            flock($cacheHandle, LOCK_EX | LOCK_NB);
            $codecache = sprintf("<?php\n//route cache \nreturn %s ;", var_export($cacheLogs, true));
            if (fwrite($cacheHandle, $codecache, strlen($codecache)) !== strlen($codecache)) {
                E("Unable to create file '%s'", $cacheLogsPath);
            }
        }
        flock($handle, LOCK_UN);
        flock($cacheHandle, LOCK_UN);
        fclose($handle);
        fclose($cacheHandle);
    }

    public function setCacheDir($cacheDir = "") {
        $cacheDir and $this->options['cacheDir'] = $cacheDir;
    }

    public function setRequest(Request $request) {
        $this->request = $request;
    }

    /**
     * 获取路由对应的URL
     * @param string $name 路由名称
     * @param array $params 路由参数
     * @param boolean $intact 是否生成完成URL,默认为否
     */
    public function getUrlForRoute($name, array $params = array(), $intact = false) {
        if (!isset($this->lists[$name]))
            E("Routing does not exist for %s ", $name);
        $routeInfo = &$this->lists[$name];
        $extra = &$routeInfo["extra"];

        $parameters = array_merge($routeInfo["params"], $this->defaultParameter, (array)$extra['default'], $params);
        $fillParameters = $extraParameters = array();
        foreach ($parameters as $parName => $parameter) { //检验路由参数
            if ("" === trim($parameter))
                E("Lack of routing parameters of '%s' for '%s'  ", $parName, $name);
            if ($routeInfo["extra"]["require"] &&
                isset($extra["require"][$parName]) &&
                !preg_match("/" . $extra["require"][$parName] . "/", $parameter)
            ) {
                E("Routing parameter format is not correct '%s' for '%s',success format: match '%s' ", $parName, $name, $extra["require"][$parName]);
            }
            if (key_exists($parName, $routeInfo["params"]))
                $fillParameters[":" . $parName] = $parameter;
            else
                $extraParameters[$parName] = $parameter;
        }
        if ($fillParameters) { //将参数填充到路由中
            uksort($fillParameters, function ($a, $b) {
                return strlen($a) < strlen($b);
            });
            $pattern = str_replace(array_keys($fillParameters), $fillParameters, $routeInfo["pattern"]);
        } else {
            $pattern = $routeInfo["pattern"];
        }

        $extra["secure"] === null and $extra["secure"] = $this->request->isSecure();
        return $this->getSchemeHttpHost($extra["secure"], $extra['domain'], $intact) .
            $this->trimdir($this->getPath($extra["route_mode"]) . $this->getPattern($pattern, $extra["route_mode"], $extra["encrypt"])) .
            $this->getQuery($pattern, $extra["route_mode"], $extra["encrypt"], $extraParameters);
    }

    private function getSchemeHttpHost($isSecure, $domain = "", $intact = false) {
        static $schemeHttpHost = array();
        if (isset($schemeHttpHost[$isSecure][$domain][$intact])) {
            return $schemeHttpHost[$isSecure][$domain][$intact];
        }
        if ($intact === false && $isSecure == $this->request->isSecure()) {
            if (empty($domain) || $domain == $this->request->getHttpHost()) {
                $schemeHttpHost[$isSecure][$domain][$intact] = "";
            } else {
                $schemeHttpHost[$isSecure][$domain][$intact] = $this->request->getScheme() . "://" . $domain;
            }
        } else {
            $scheme = $isSecure ? "https://" : "http://";
            $schemeHttpHost[$isSecure][$domain][$intact] = $scheme . (empty($domain) ? $this->request->getHttpHost() : $domain);

        }
        return $schemeHttpHost[$isSecure][$domain][$intact];
    }

    private function getPath($urlMode = Route::ROUTE_PATHINFO) {
        return isset($this->modePaths[$urlMode]) ? $this->modePaths[$urlMode] : $this->request->server->get("SCRIPT_NAME");
    }

    private function getPattern($pattern, $mode, $encrypt) {
        if ($mode == Route::ROUTE_NORMAL) {
            return "";
        }
        return $encrypt ? $this->enEncrypt($pattern, $encrypt) : $pattern;
    }

    private function getQuery($pattern, $mode, $encrypt, $extraParameters) {
        if ($mode == Route::ROUTE_NORMAL) {
            if (isset($extraParameters[$this->options["var_name"]]))
                unset($extraParameters[$this->options["var_name"]]);
            $extraParameters = array_merge(array($this->options["var_name"] => $pattern), $extraParameters);
        }
        if (empty($extraParameters))
            return "";
        return "?" . ($encrypt ? $this->enEncrypt(http_build_query($extraParameters), $encrypt) : http_build_query($extraParameters));
    }

    public function getActionForRoute($name, $params = array()) {
        if (!isset($this->lists[$name]))
            return false;
        $routeInfo = &$this->lists[$name];
        $callback = &$routeInfo['callback'];
        $args = array();
        foreach ($params as $key => $par) {
            if (false !== strpos($callback, "{" . $key . "}")) {
                $callback = str_replace("{" . $key . "}", $par, $callback);
            } else {
                $args[$key] = $par;
            }
        }
        ksort($args);
        list($app, $controller_name, $action_name) = explode(":", $callback);
        return array(
            "app" => $app,
            "controller" => ucfirst($controller_name),
            "action" => $action_name,
            "class" => $app . "\\Controller\\" . ucfirst($controller_name) . "Controller",
            "params" => $args
        );
    }

    public function has($name) {
        return isset($this->lists[$name]);
    }

}
