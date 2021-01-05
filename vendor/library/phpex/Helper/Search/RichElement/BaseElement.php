<?php
/**
 * Created by PhpStorm.
 * User: river2liu
 * Date: 2017/3/17
 * Time: 11:37
 */

namespace phpex\Helper\Search\RichElement;


abstract class BaseElement {
    protected $elementKey;
    protected $elementLabel;
    protected $realKey;

    protected $data = array();

    protected $prefix = "";

    protected $suffix = array();

    protected $parameters = null;

    public function __construct($key, $label, $prefix) {
        $this->elementKey = $key;
        $this->elementLabel = $label;
        $this->prefix = $prefix;
        $this->realKey = $prefix . str_replace(".", "_", $key);
    }

    public abstract function where();

    public function parameter() {
        if (null === $this->parameters) $this->where();
        return $this->parameters;
    }

    public abstract function form();

    public function setData($data) {
        $this->data = $data;
    }

    public function getValues() {
        if (!$this->suffix) {
            return isset($this->data[$this->realKey]) ? $this->data[$this->realKey] : null;
        }
        $values = array();
        foreach ($this->suffix as $suffix) {
            $queryKey = sprintf("%s_%s", $this->realKey, $suffix);
            $values[$queryKey] = isset($this->data[$queryKey]) ? $this->data[$queryKey] : null;
        }
        return $values;
    }

}
