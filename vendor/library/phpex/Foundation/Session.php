<?php

namespace phpex\Foundation;

/**
 * Description of Session
 *
 * @author Administrator
 */
class Session extends ParameterBag {

    protected $started = false;

    /**
      +----------------------------------------------------------
     * 启动Session
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    public function start() {
        if ($this->started)
            return;
        session_start();
        $this->parameters = $_SESSION;
        $this->started = true;
    }

    public function isStarted() {
        if (isset($_SESSION)) {
            $this->started = true;
            $this->parameters = $_SESSION;
        }
        return $this->started;
    }

    /**
      +----------------------------------------------------------
     * 清空Session
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @return void
      +----------------------------------------------------------
     */
    function clear() {
        $this->parameters = $_SESSION = array();
        session_destroy();
    }

    function __construct(array $parameters = array()) {
        ini_set('session.auto_start', 0);
        // 设置Session有效域名
        $this->setCookieDomain($parameters["cookie_domain"]);
        $this->name($parameters["session_name"]);
        $this->path($parameters["session_path"]);
        $this->setCallback($parameters["session_callback"]);
        if ($parameters["session_auto_start"])
            $this->start();
        parent::__construct(array());
    }

    /**
     * 设置或返回session_id
     * @param type $session_id
     * @return string
     */
    function session_id($session_id = null) {
        return !is_null($session_id) ? session_id($session_id) : session_id();
    }

    /**
      +----------------------------------------------------------
     * 设置Session cookie_domain
     * 返回之前设置
      +----------------------------------------------------------
     * @param string $sessionDomain
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    function setCookieDomain($sessionDomain = null) {
        $return = ini_get('session.cookie_domain');
        if (!empty($sessionDomain)) {
            ini_set('session.cookie_domain', $sessionDomain); //跨域访问Session
        }
        return $return;
    }

    function detectID() {
        if (session_id() != '') {
            return session_id();
        }
        if ($this->useCookies()) {
            if (isset($_COOKIE[$this->name()])) {
                return $_COOKIE[$this->name()];
            }
        } else {
            if (isset($_GET[$this->name()])) {
                return $_GET[$this->name()];
            }
            if (isset($_POST[$this->name()])) {
                return $_POST[$this->name()];
            }
        }
        return null;
    }

    /**
      +----------------------------------------------------------
     * 设置Session 是否使用cookie
     * 返回之前设置
      +----------------------------------------------------------
     * @param boolean $useCookies  是否使用cookie
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @return boolean
      +----------------------------------------------------------
     */
    public function useCookies($useCookies = null) {
        $return = ini_get('session.use_cookies') ? true : false;
        if (isset($useCookies)) {
            ini_set('session.use_cookies', $useCookies ? 1 : 0);
        }
        return $return;
    }

    /**
      +----------------------------------------------------------
     * 设置或者获取当前Session name
      +----------------------------------------------------------
     * @param string $name session名称
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string 返回之前的Session name
      +----------------------------------------------------------
     */
    function name($name = null) {
        return isset($name) ? session_name($name) : session_name();
    }

    /**
      +----------------------------------------------------------
     * 设置或者获取当前Session保存路径
      +----------------------------------------------------------
     * @param string $path 保存路径名
      +----------------------------------------------------------
     * @access public
      +----------------------------------------------------------
     * @return string
      +----------------------------------------------------------
     */
    public function path($path = null) {
        empty($path)or is_dir($path) or mkdir($path, 0777, true);
        return !empty($path) ? session_save_path($path) : session_save_path();
    }

    /**
      +----------------------------------------------------------
     * 设置Session 对象反序列化时候的回调函数
     * 返回之前设置
      +----------------------------------------------------------
     * @param string $callback  回调函数方法名
      +----------------------------------------------------------
     * @static
     * @access public
      +----------------------------------------------------------
     * @return boolean
      +----------------------------------------------------------
     */
    public function setCallback($callback = null) {
        $return = ini_get('unserialize_callback_func');
        if (!empty($callback)) {
            ini_set('unserialize_callback_func', $callback);
        }
        return $return;
    }

    public function save() {
        $_SESSION = $this->parameters;
    }

    public function __destruct() {
        $this->save();
    }

    public function set($key, $value) {
        parent::set($key, $value);
        return $this;
    }

    public function remove($key) {
        parent::remove($key);
        return $this;
    }

    function switchSession($sessionName) {
        session_write_close();
        Q()->getSession()->name($sessionName);
        if (Q()->cookies->has($sessionName)) {
            $this->session_id(Q()->cookies->get($sessionName));
        } else {
            $this->session_id(substr(sha1(mt_rand()), 0, 32));
        }
        session_start();
        $this->parameters = $_SESSION;
    }

    function switchAppSession($appName) {
        if (ins()->has("app." . ucfirst($appName))) {
            /* @var $app \phpex\Library\App */
            $app = ins()->get("app." . ucfirst($appName));
        } else {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            throws("App 不存在", $trace[0]["file"], $trace[0]["line"], $trace[1]["class"] . "::" . $trace[1]["function"]);
        }
        $this->switchSession($app->getSessionName());
    }

}
