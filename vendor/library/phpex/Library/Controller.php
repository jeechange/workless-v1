<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Library;

use phpex\Foundation\Response;
use phpex\Library\View;
use phpex\Access\Access;

/**
 * Description of Controller
 *
 * @author Administrator
 */
abstract class Controller {

    /**
     * @var View
     */
    protected $view;

    /**
     *
     * @var Access
     */
    protected $access;

    /**
     *
     * @var Response
     */
    private $response;
    protected $debug = false;

    /**
     *
     * @var \phpex\Helper\Search\Search[]
     */
    private $searches = array();

    public function get($name) {
        return ins()->get($name);
    }

    /**
     * @return static
     */
    public static function _new() {

    }

    public function setAccess(Access $access) {
        $this->access = $access;
        $this->autoCreateDModel();
        $this->_initialize();
    }

    /**
     * @return View
     */
    public function getView() {
        if (null === $this->view) {
            if (!ins()->has("core.view")) {
                $this->view = new View();
                ins()->addInstance("core.view", $this->view);
            } else {
                $this->view = ins()->get("core.view");
            }
        }
        return $this->view;
    }

    public function assign($key, $val = '') {
        $this->getView()->assign($key, $val);
    }

    public function openDebug() {
        $this->debug = true;
    }

    public function getAssign($key = '') {
        return $this->getView()->getAssign($key);
    }

    protected function _initialize() {

    }

    protected function redirect($url, $msg = "", $time = 0, $status = 302) {
        if (R()->has($url))
            $url = url($url);

        if (empty($url)) {
            throw new \InvalidArgumentException('Cannot redirect to an empty URL.');
        }
        $response = new Response();
        $response->setStatusCode($status);
        if ($time > 0)
            $response->setContent(
                sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="refresh" content="%3$d;url=%1$s" />

        <title>Redirecting to %1$s</title>
    </head>
    <body>
    <h1>%2$s</h1>
    <p>系统将在%3$d秒之后自动跳转到 <a href="%1$s">%1$s</a>！</p>      
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'), $msg ?: "Redirecting", $time));

        $response->headers->set('Location', $url);
        if (!$response->isRedirect()) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code is not a redirect ("%s" given).', $status));
        }
        return $response;
    }

    /**
     *
     * @return Response
     */
    protected function getResponse($content = "", $status = 200) {
        if (null === $this->response) {
            $contentType = C("response.contentType");
            $charset = C("response.charset");
            $headers = array("Content-Type" => $contentType . ";charset=" . $charset);
            $this->response = new Response("", $status, $headers);
        }
        $this->response->setContent($content);
        return $this->response;
    }

    protected function show($content) {
        $content = $this->getView()->parse($content);
        $this->getResponse()->setContent($content);
        return $this->response;
    }

    /**
     * @param string $templateFile
     * @param array $assign
     * @return string
     */
    protected function fetch($templateFile = "", array $assign = null) {
        return $this->getView()->fetch($templateFile, $assign);
    }

    /**
     * @param string $templateFile
     * @param array $assign
     * @return Response
     */
    protected function display($templateFile = '', array $assign = null, $main = true) {
        $content = $this->fetch($templateFile, $assign);
        if ($main)
            $this->addJumpTips($content);
        $this->getResponse();

        if ($main && Q()->server->has('HTTP_ACCEPT_ENCODING') && false !== strpos(Q()->server->get("HTTP_ACCEPT_ENCODING"), "gzip") && extension_loaded("zlib")) {
            $content = gzencode($content, 9);
            $this->response->headers->set("Content-Encoding", "gzip");
            $this->response->headers->set("Vary", "Accept-Encoding");
            $this->response->headers->set("Content-Length", strlen($content));
        }
        $this->response->setContent($content);
        if (!$this->response->getCharset())
            $this->response->setCharset(C("response.charset"));
        return $this->response;
    }

    /**
     * 返回成功页面，如果用户的请求为ajax方式，则只有第一个参数有效
     * @param string|array $message
     * @param string $jumpUrl
     * @param integer $time
     * @param string $tpl
     * @return Response
     */
    protected function success($message = '', $jumpUrl = '', $time = 1, $tpl = "success_tpl") {
        if ($jumpUrl === "") {
            $jumpUrl = $this->getRequest()->server->get("HTTP_REFERER");
        }
        if (Q()->isAjax()) {
            return $this->ajaxReturn(array("status" => "y", "data" => $message));
        }
        $app_core_controller_jump_tips = array(
            "status" => "success",
            "tpl" => $tpl,
            "time" => $time,
            "interval" => 1000,
            "referer" => Q()->getUri(),
            "title" => array("操作成功", "操作失败"),
            "msg" => $message
        );
        $this->setSession("app_core_controller_jump_tips", $app_core_controller_jump_tips);
        return $this->redirect($jumpUrl);
    }

    /**
     * 返回错误页面，如果用户的请求为ajax方式，则只有第一个参数有效
     * @param string|array $message
     * @param string $jumpUrl
     * @param integer $time
     * @param string $tpl
     * @return Response
     */
    protected function error($message = '', $jumpUrl = '', $time = 3, $tpl = "error_tpl") {
        if ($jumpUrl === "") {
            $jumpUrl = $this->getRequest()->server->get("HTTP_REFERER");
        }
        if (Q()->isAjax()) {
            return $this->ajaxReturn(array("status" => "n", "info" => $message));
        }

        if ($this->debug) {
            $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $from = "<pre><b>" . dirtrim($debug[0]["file"]) . ";line:" . $debug[0]["line"] . "</b>\n";
            return $this->show($from . $message . "</pre>");
        }
        $app_core_controller_jump_tips = array(
            "status" => "error",
            "tpl" => $tpl,
            "time" => $time,
            "interval" => 1000,
            "referer" => Q()->getUri(),
            "title" => array("操作成功", "操作失败"),
            "msg" => $message
        );
        $this->setSession("app_core_controller_jump_tips", $app_core_controller_jump_tips);
        return $this->redirect($jumpUrl);
    }

    protected function fail($message) {
        $this->assign("message", $message);
        return $this->display(C("template.fail") ?: C("view.fail"));
    }

    private function addJumpTips(&$content) {
        if (!Q()->getSession() || !Q()->getSession()->has("app_core_controller_jump_tips"))
            return;
        $app_core_controller_jump_tips = Q()->getSession()->get("app_core_controller_jump_tips");
//        if ($app_core_controller_jump_tips["referer"] != $this->getRequest()->server->get("HTTP_REFERER"))
//            return;
        $file = C("view." . $app_core_controller_jump_tips["tpl"]);
        $tips = $this->getView()->fetch($file, (array)$app_core_controller_jump_tips);
        Q()->getSession()->remove("app_core_controller_jump_tips");
        Q()->getSession()->save();
        if (!$tips)
            return;
        $content = str_replace("</body>", $tips . "</body>", $content);
    }

    protected function ajaxResponse() {

    }

    protected function ajaxReturn($data, $type = "") {
        if (empty($type))
            $type = C('response.ajax_type');
        switch (strtoupper($type)) {
            case 'XML' :
                $headers = array("Content-Type" => "text/xml;charset=utf-8");
                return new Response(xml_encode($data), 200, $headers);
            case 'JSONP':
                // 返回JSON数据格式到客户端 包含状态信息               
                $handler = isset($_GET[C('response.jsonpHandlerVar')]) ? $_GET[C('response.jsonpHandlerVar')] : C('response.jsonpHandler');
                $headers = array("Content-Type" => C('response.jsonContentType') . ";charset=utf-8");
                return new Response($handler . '(' . json_encode($data) . ');', 200, $headers);
            case 'EVAL' :
                // 返回可执行的js脚本               
                $headers = array("Content-Type" => "text/html;charset=utf-8");
                return new Response(json_encode($data), 200, $headers);
            case 'CORS' :
                $headers = array(
                    "Content-Type" => C('response.jsonContentType') . ";charset=utf-8",
                    "Access-Control-Allow-Origin" => "*",
                    "Access-Control-Allow-Methods" => "GET,POST,OPTIONS",
                    "Access-Control-Allow-Credentials" => "true",
                    "Access-Control-Allow-Headers" => "x-requested-with,content-type"
                );
                return new Response(json_encode($data), 200, $headers);
            case 'JSON' :
            default;
                // 返回JSON数据格式到客户端 包含状态信息                
                $headers = array("Content-Type" => C('response.jsonContentType') . ";charset=utf-8");
                return new Response(json_encode($data), 200, $headers);
        }
    }

    /**
     *
     * @param type $key
     */
    protected function getUser($key = null) {
        return $this->access->getUser($key);
    }

    protected function getRequest() {
        return Q();
    }

    protected function setSession($key, $value) {
        Q()->getSession()->set($key, $value)->save();
        return $this;
    }

    /**
     * 表单助手
     * @param type $name
     * @param type $action
     * @param type $method
     * @return \phpex\Helper\Form\Form
     */
    protected function form($name, $action = "", $method = "post") {
        return new \phpex\Helper\Form\Form($name, $action, $method);
    }

    /**
     * 搜索框助手
     * @param type $name
     * @return \phpex\Helper\Search\Search
     */
    final protected function search($name = "search") {
        if (!isset($this->searches[$name]))
            $this->searches[$name] = new \phpex\Helper\Search\Search($this);
        return $this->searches[$name];
    }

    protected function flushUser() {
        $this->access->flushUser();
    }

    protected function autoCreateDModel() {
        $objRef = new \ReflectionClass($this);
        $vars = $objRef->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        $docMatch = array();
        foreach ($vars as $var) {
            $doc = $var->getDocComment();
            if (!$doc) {
                continue;
            }
            if (preg_match("#^\s*\*\s+@var\s+(?<class>\\\\Admin\\\\DModel\\\\[A-Z][a-zA-Z_0-9]+DModel)\s*$#m", $doc, $docMatch)) {
                $this->{$var->name} = new $docMatch["class"];
            }

        }
    }

}
