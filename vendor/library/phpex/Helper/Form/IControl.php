<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form;

/**
 *
 * @author Administrator
 */
interface IControl {

    public function renderBegin();

    public function renderLabel();

    public function renderControl();

    public function renderTip();

    public function renderEnd();

    public function render();

    public function addRule($name, $message = "", $parameter = null);

    public function setDefaultValue($value);

    public function getDefaultValue();

    public function addClass($class);

    public function __construct(Form $form, $name, $label);

    public function setName($name);

    public function setForm(Form $form);

    public function setHint($emptymsg = "", $successmsg = "", $errormsg = "");

    public function setReadonly($value = true);

    public function setDisabled($value = true);

    public function setAttribute($name, $value);
}
