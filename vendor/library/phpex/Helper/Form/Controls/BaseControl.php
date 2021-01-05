<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form\Controls;

use phpex\Helper\Form\IControl;
use phpex\Helper\Form\Wrap;
use phpex\Helper\Form\Form;

/**
 * Description of BaseControl
 *
 * @author Administrator
 */
abstract class BaseControl implements IControl {

    protected $form;
    protected $attributes = array();
    protected $readonly = false;
    protected $disabled = false;
    protected $name;
    protected $class = "";
    protected $label;
    protected $defaultValue;

    public function __construct(Form $form, $name, $label) {
        $this->form = $form;
        $this->name = $name;
        $this->label = $label;
    }

    /**
     * @var Wrap 
     */
    protected $wrap;

    public function render() {
        return $this->renderBegin() .
                $this->renderLabel() .
                $this->renderControl() .
                $this->renderTip() .
                $this->renderEnd();
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setForm(Form $form) {
        $this->form = $form;
    }

    public function renderBegin() {
        return $this->form->getWrap()->begin("control", $this->name);
    }

    abstract protected function generateControl();

    public function renderControl() {
        return $this->form->getWrap()->begin("element", $this->name) .
                $this->generateControl() .
                $this->form->getWrap()->end("element");
    }

    public function renderEnd() {
        return $this->form->getWrap()->end("control");
    }

    public function generateLabel() {
        return sprintf('<label for="%s" class="tfrm-label">%s</label>', $this->form->getWrap()->generateKey($this->name), $this->label? : $this->name) . "\n";
    }

    public function renderLabel() {
        return $this->form->getWrap()->begin("label", $this->name) . $this->generateLabel() . $this->form->getWrap()->end("label");
    }

    public function renderTip() {
        $tip = sprintf('<span id="frm_tip_msg_%s" class="frm-tip-msg"></span>', $this->form->getWrap()->generateKey($this->name));
        return $this->form->getWrap()->begin("tip", $this->name) . $tip . "\n" . $this->form->getWrap()->end("tip");
    }

    public function addClass($class) {
        $classes = explode(" ", $this->class);
        $addcalsses = explode(" ", $class);
        $allcalsses = array_merge($classes, $addcalsses);
        $allcalsses = array_unique($allcalsses);
        $this->class = join(" ", $allcalsses);
        return $this;
    }

    public function addRule($name, $message = "", $parameter = null) {
        $args = func_get_args();
        $rule = $this->form->getRule();
        array_unshift($args, $this->name);
        call_user_func_array(array(&$rule, "addRules"), $args);
        return $this;
    }

    /**
     * 
     * @param string $emptymsg
     * @param string $successmsg
     * @param string $errormsg
     * @return IControl
     */
    public function setHint($emptymsg = "", $successmsg = "", $errormsg = "") {
        $this->form->getRule()->addRules($this->name, 0, $emptymsg, $successmsg, $errormsg);
        return $this;
    }

    public function setDefaultValue($value) {
        $this->defaultValue = $value;
        return $this;
    }

    public function getDefaultValue() {
        return $this->defaultValue;
    }

    public function setReadonly($value = true) {
        $this->readonly = $value;
        return $this;
    }

    public function setDisabled($value = true) {
        $this->disabled = $value;
        return $this;
    }

    public function setAttribute($name, $value) {
        $this->attributes[$name] = $value;
        return $this;
    }

}
