<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form;

use phpex\Helper\Form\Controls;

/**
 * Description of Container
 *
 * @author Administrator
 */
class Container implements \ArrayAccess {

    /**
     * @var Wrap
     */
    protected $wrap;

    /**
     * @var Csrf 
     */
    protected $csrf;

    /**
     * @var Rule 
     */
    protected $rule;

    /**
     * @var IControl[] 
     */
    protected $controls = array();

    /**
     *
     * @var Controls\Button[]
     */
    protected $buttons = array();

    public function __construct() {
        $this->wrap = new Wrap();
        $this->csrf = new Csrf();
        $this->rule = new Rule();
    }

    /**
     * 添加普通文本框
     * @param string $name
     * @param string $label
     * @param integer $cols
     * @param integer $maxLength
     * @return Controls\Text
     */
    public function addText($name, $label = NULL, $cols = NULL, $maxLength = NULL) {
        $control = new Controls\Text($this, $name, $label, $maxLength);
        if ($cols) {
            $control->setAttribute('size', $cols);
        }
        return $this[$name] = $control;
    }

    /**
     * Adds multi-line text input control to the form.
     * @param  string  control name
     * @param  string  label
     * @param  int  width of the control
     * @param  int  height of the control in text lines
     * @return Controls\TextArea
     */
    public function addTextArea($name, $label = NULL, $cols = NULL, $rows = NULL) {
        return $this[$name] = new Controls\TextArea($this, $name, $label, $cols, $rows);
    }

    /**
     * 添加下拉框
     * @param string $name
     * @param string $label
     * @param array $options
     * @param integer $size
     * @return Controls\Select
     */
    public function addSelect($name, $label = NULL, array $options = array(), $size = 1) {
        $control = new Controls\Select($this, $name, $label, $options, $size);
        return $this[$name] = $control;
    }

    /**
     * 添加密码框
     * @param string $name
     * @param string $label
     * @param integer $cols
     * @param integer $maxLength
     * @return Controls\Password
     */
    public function addPassword($name, $label = NULL, $cols = NULL, $maxLength = NULL) {
        $control = new Controls\Password($this, $name, $label, $maxLength);
        if ($cols) {
            $control->setAttribute('size', $cols);
        }
        return $this[$name] = $control;
    }

    /**
     * 添加一个普通按钮
     * @param string $name
     * @param string $caption
     * @return Controls\Button
     */
    public function addButton($name, $caption = NULL) {
        if (!$this->buttons[$name])
            $this->buttons[$name] = new Controls\Buttons($this, $name, "");
        return $this->buttons[$name]->addButton($caption);
    }

    /**
     * 添加一个提交按钮
     * @param string $name
     * @param string $caption
     * @return Controls\Button
     */
    public function addSubmit($name, $caption = NULL) {
        if (!isset($this->buttons[$name]))
            $this->buttons[$name] = new Controls\Buttons($this, $name, "");
        return $this->buttons[$name]->addSubmit($caption);
    }

    /**
     * 添加一个重置按钮
     * @param string $name
     * @param string $caption
     * @return Controls\Button
     */
    public function addResetButton($name, $caption = NULL) {
        if (!$this->buttons[$name])
            $this->buttons[$name] = new Controls\Buttons($this, $name, "");
        return $this->buttons[$name]->addResetButton($caption);
    }

    /**
     * 添加复选框
     * @param string $name
     * @param string $label
     * @param array $items
     * @return Controls\Select
     */
    public function addCheckbox($name, $label = NULL, array $items = array()) {
        return $this[$name] = new Controls\Checkbox($this, $name, $label, $items);
    }

    /**
     * 添加复选框
     * @param string $name
     * @param string $label
     * @param array $items
     * @return Controls\Select
     */
    public function addRadio($name, $label = NULL, array $items = array()) {
        return $this[$name] = new Controls\Radio($this, $name, $label, $items);
    }

    public function offsetExists($offset) {
        return isset($this->controls[$offset]);
    }

    public function offsetGet($offset) {
        return isset($this->controls[$offset]) ? $this->controls[$offset] : null;
    }

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->controls[] = $value;
        } else {
            $this->controls[$offset] = $value;
        }
    }

    public function offsetUnset($offset) {
        unset($this->controls[$offset]);
    }

    /**
     * @return Wrap
     */
    public function getWrap() {
        return $this->wrap;
    }

    /**
     * @return Csrf
     */
    public function getCsrf() {
        return $this->csrf;
    }

    /**
     * @return Rule
     */
    public function getRule() {
        return $this->rule;
    }

}
