<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form;

/**
 * Description of Script
 *
 * @author Administrator
 */
class Rule {

    const REQUIR = 'requir'; //必填规则
    const UNIQUE = 'unique'; //唯一规则
    const EMAIL = 'email';  //邮件规则
    const URL = "url"; //URL地址规则
    const CURRENCY = 'currency'; //货币规则
    const NUM = 'num'; //整数规则
    const NUM1 = 'num1'; //正整数
    const NUM2 = 'num2'; //负整数
    const DECIMAL = 'decimal'; //两位小数
    const FLOAT = 'float'; //浮点值
    const LEN = 'len'; //指定长度
    const CONFIRM = 'confirm'; //值确认
    const EQUAL = 'equal'; //是否等于某个值
    const MIN = 'min'; //允许的最小值
    const MAX = 'max'; //允许的最大值
    const TEL= 'tel'; //手机号码规则

    protected $internalRules = array(
        Rule::REQUIR => '/\S+/',
        Rule::UNIQUE => 'Unique',
        Rule::EMAIL => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', //邮件规则
        Rule::URL => '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/', //URL地址规则
        Rule::CURRENCY => '/^\d+(\.\d+)?$/', //货币规则
        Rule::NUM => '/^\-?\d+$/', //整数规则
        Rule::NUM1 => '/^\+?\d+$/', //正整数
        Rule::NUM2 => '/^\-\d+$/', //负整数
        Rule::DECIMAL => '/^\-?\d+(\.\d{1,2})?$/', //两位小数
        Rule::FLOAT => '/^\-?\d+(\.\d+)?$/', //两位小数
        Rule::LEN => 'Len', //指定长度
        Rule::CONFIRM => 'Confirm', //值确认
        Rule::EQUAL => 'Equal', //是否等于某个值
        Rule::MIN => 'Min', //允许的最小值
        Rule::MAX => 'Max', //允许的最大值
        Rule::TEL => '/^1[3-8]\d{9}$/', //允许的最大值
    );
    protected $extendRules = array();
    protected $rules = array();
    protected static $writeSrc = array();
    protected $extendSrc = array();
    protected $formId;
    protected $error;

    public function addRules($vName, $ruleName, $message = "", $params = "") {
        $args = func_get_args();
        array_shift($args);
        array_shift($args);
        if (0 === $ruleName || !empty($ruleName))
            $this->rules[$vName][$ruleName] = $args;
        return $this;
    }

    public function setFormId($formId) {
        $this->formId = $formId;
        return $this;
    }

    /**
     * 扩展验证规则
     * @param string $name
     * @param string|callback $callback 验证的正则表达式或回调函数
     * @param string $jsSrc js中要加载的源地址
     * @return Rule
     */
    public function extendRule($name, $callback, $jsSrc = null) {
        if (isset($this->internalRules[$name])) {
            $debug = debug_backtrace();
            throws('扩展规则名不能与内置规则名相同:"%s"', $debug['file'], $debug[0]["line"], "", $name);
        }
        if ((is_string($callback) && $callback{0} != "/") && !is_callable($callback)) {
            throws('扩展规则必须是可回调的函数或方法:"%s"', $debug['file'], $debug[0]["line"], "", $name);
        }
        $this->extendRules[$name] = $callback;
        if ($jsSrc && !in_array($jsSrc, $this->extendSrc))
            $this->extendSrc[] = $jsSrc;
        return $this;
    }

    public function getError() {
        return $this->error;
    }

    public function isValid(array $datas) {
        foreach ($this->rules as $name => $rule) {
            $value = isset($datas[$name]) ? $datas[$name] : null;
            foreach ($rule as $n => $params) {
                $msg = array_shift($params);
                if ($n == 0)
                    continue;
                if (empty($value) && $n != Rule::REQUIR)
                    continue;
                $match = isset($this->internalRules[$n]) ? $this->internalRules[$n] : (isset($this->extendRules[$n]) ? $this->extendRules[$n] : null);
                if (is_null($match)) {
                    $this->error = sprintf('校验规则未定义:"%s"', $n);
                    return false;
                }
                if (is_callable($match)) {
                    $valid = method_exists($this, "match" . $match) ?
                            $this->{"match" . $match}($value, $params, $datas) :
                            call_user_func($match, $value, $params, $datas);
                } elseif (is_string($match) && $match{0} == "/") {
                    try {
                        $valid = preg_match($match, (string) $value);
                    } catch (\Exception $ex) {
                        $this->error = sprintf('校验规则语法错误:"%s",错误信息:"%s"', $match, $ex->getMessage());
                        return false;
                    }
                } else {
                    $this->error = sprintf('校验规则未定义:"%s"', $match);
                    return false;
                }
                if (!$valid) {
                    $this->error = $msg;
                    return false;
                }
            }
        }
        return true;
    }

    protected function matchLen($value, $params) {
        $len = is_array($value) ? count($value) : (is_scalar($value) ? strlen($value) : null);
        if (is_null($len))
            return false;
        if (is_string($params)) {
            $params = explode(",", $params);
        }
        $min = (int) $params[0];
        $max = (int) $params[1];
        $min < 0 and $min = 0;
        $max == 0 and $max = 65535;
        $max < $min and $max = 65535;
        return $len >= $min && $len <= $max;
    }

    protected function matchConfirm($value, $params, $datas = array()) {
        return $value == $datas[$params];
    }

    protected function matchEqual($value, $params) {
        return $value == $params;
    }

    protected function matchMin($value, $params) {
        return $value >= $params;
    }

    protected function matchMax($value, $params) {
        return $value <= $params;
    }

    protected function matchUnique($value, $params, $datas) {
        $callback = $params[0];
        if (is_callable($callback)) {
            return call_user_func($callback, $value, $datas);
        }
        return false;
    }

    public function __toString() {
        $code = "";
        foreach ($this->extendSrc as $src) {
            if (in_array($src, self::$writeSrc))
                continue;
            $code.=sprintf('<script type="text/javascript" src="%s"></script>', StrFilter::url($src));
        }
        $code .= '<script type="text/javascript">$("#%s").phpexForm(%s)</script>';
        return sprintf($code, $this->formId, StrFilter::js($this->rules));
    }

}
