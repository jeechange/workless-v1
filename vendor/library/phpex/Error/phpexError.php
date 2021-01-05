<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Error;

use phpex\Foundation\Response;

/**
 * Description of phpexError
 *
 * @author Administrator
 */
class phpexError {

    static public $httpStatus = 500;
    static public $context = array();

    public static function fatalError() {
        if ($e = error_get_last()) {
            self::appError($e['type'], $e['message'], $e['file'], $e['line'], 1);
        }
    }

    static public function getFileSerial($filepath) {
        $file = main()->getRuntime() . "/FileSerial.log";
        $filepath = realpath($filepath);
        $serial = file_get_contents($file);
        $lists = explode("\r\n", $serial);
        $compare = md5($filepath);
        foreach ($lists as $line) {
            list($linecode, $md5code, $fileinfo) = explode("#", $line);
            if (!$linecode || !$md5code || !$fileinfo) {
                continue;
            }
            if ($md5code == $compare) {
                return $linecode;
            }
        }
        $linecode or $linecode = 0;
        $linecode++;
        file_put_contents($file, ltrim($serial) . "\r\n" . $linecode . "#" . $compare . "#" . $filepath);
        return $linecode;
    }

    static public function appError($errno, $errstr, $errfile, $errline, $type = 0) {
        $debug = main()->getDebug();
        self::$context = $context = array(
            'levels' => $errno,
            'code' => self::getFileSerial($errfile) . "-" . $errline . "-" . date("dH"),
        );
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $errorStr = "$errstr " . $errfile . " in Line $errline.";
                if ($debug || $type == 1)
                    logger(\Monolog\Logger::ERROR, $errno)->addError($errorStr, $context);
                break;
            case E_USER_WARNING:
                $errorStr = "[$errno] $errstr " . $errfile . " in line: $errline .";
                if ($debug || $type == 1)
                    logger(\Monolog\Logger::WARNING, $errno)->addWarning($errorStr, $context);
                break;
            case E_STRICT:
            case E_USER_NOTICE:
            default:
                $errorStr = "[$errno] $errstr " . $errfile . " in line: $errline .";
                if ($debug || $type == 1)
                    logger(\Monolog\Logger::NOTICE, $errno)->addNotice($errorStr, $context);
                return;
        }
        self::halt($errorStr, $type);
    }

    public static function appException($e) {
        $error = array();
        $error['message'] = $e->getMessage();
        $trace = $e->getTrace();
        if ('E' == $trace[0]['function']) {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        } else {
            $error['file'] = $e->getFile();
            $error['line'] = $e->getLine();
        }
        $error['trace'] = $e->getTraceAsString();

        self::$context = $context = array(
            'levels' => E_ERROR,
            'code' => self::getFileSerial($error['file']) . "-" . $error['line'] . "-" . date("dH"),
        );

        $context["file"] = $error['file'];
        $context["line"] = $error['line'];
        logger(\Monolog\Logger::ERROR, E_ERROR)->addError($error['message'], $context);
        self::halt($error, 1);
    }

    public static function halt($error) {
        $env = main()->getEnv();
        $config = C("error");
        if (main()->getDebug()) {
            if (!is_array($error)) {
                $trace = debug_backtrace();
                $e['message'] = $error;
                $e['file'] = $trace[0]['file'];
                $e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
                $e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';
                $e['line'] = $trace[0]['line'];
                $traceInfo = '';
                $time = date('y-m-d H:i:m');
                foreach ($trace as $t) {
                    $traceInfo .= '[' . $time . '] ';
                    if (isset($t['file']))
                        $traceInfo.= $t['file'];
                    if (isset($t['file']))
                        $traceInfo.= ' (' . $t['line'] . ') ';
                    if (isset($t['class']))
                        $traceInfo.= $t['class'];
                    if (isset($t['type']))
                        $traceInfo.= $t['type'];
                    if (isset($t['function']))
                        $traceInfo.= $t['function'];
                    $traceInfo .=' parameters (" ';
                    if (is_array($t['args']) && !empty($t['args'])) {
                        foreach ($t['args'] as $arg) {
                            $traceInfo .= is_object($arg) ? 'object(' . get_class($arg) . ') , ' : (is_scalar($arg) ? $arg . " , " : "array() , ");
                        }
                        $traceInfo = rtrim($traceInfo, ", ") . " ";
                    }
                    $traceInfo .='")<br/>';
                }
                $e['trace'] = $traceInfo;
            } else {
                $e = $error;
            }
        } else {
            if (isset($config['show_error_msg']) && $config['show_error_msg'])
                $e['message'] = is_array($error) ? $error['message'] : $error;
            else
                $e['message'] = isset($config['error_msg']) ? $config['error_msg'] : "";
        }

        $error_page = isset($config['error_page']) ? $config['error_page'] : null;
        if (Q()->isAjax()) {
            header(sprintf('HTTP/1.1 %d %s', 200, Response::$statusTexts[200]));
            echo json_encode(array("status" => "n", "info" => "抱歉，您访问的页面出现错误，跟踪代码：" . self::$context["levels"] . "-" . self::$context["code"]));
            exit;
        }
        if (!empty($error_page) && $env == "prod" && is_file($error_page)) {
            header(sprintf('HTTP/1.1 %d %s', self::$httpStatus, Response::$statusTexts[self::$httpStatus]));
            include $error_page;
            exit;
        }
        if (isset($config['tmpl_exception_file'][$env])) {
            header(sprintf('HTTP/1.1 %d %s', self::$httpStatus, Response::$statusTexts[self::$httpStatus]));
            include $config['tmpl_exception_file'][$env];
        } else {
            $server_admin = Q()->server->get("SERVER_ADMIN");
            $response = new Response("服务器内部错误，详情请联系:$server_admin", self::$httpStatus, array("Content-Type" => "text/html;charset=utf-8"));
            $response->send();
        }
        exit;
    }

}
