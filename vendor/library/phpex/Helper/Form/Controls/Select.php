<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form\Controls;
use phpex\Helper\Form\Form;

/**
 * Description of Select
 *
 * @author Administrator
 */
class Select extends BaseControl {
    
    protected $defaultValue=array();

    protected $options = array();
    protected $optiondisabled = array();
    protected $optionreadonly = array();

    public function __construct(Form $form, $name, $label,  $options = array(), $size = 1) {
        parent::__construct($form, $name, $label);
        $this->class = "frm-select";
        $this->attributes["size"] = intval($size);
        $this->options = $options;
    }

    protected function generateControl() {
        $options = "\n";

        foreach ($this->options as $val => $label) {
            $optionargs = array(
                $val,
                in_array($val, $this->optionreadonly) ? ' readonly="readonly"' : '',
                in_array($val, $this->optiondisabled) ? ' disabled="disabled"' : '',
                in_array($val, $this->defaultValue) ? ' selected="selected"' : '',
                $label,
            );
            $options.=vsprintf('<option value="%s"%s%s%s>%s</option>', $optionargs) . "\n";
        }
        $args = array(
            $this->form->getWrap()->generateKey($this->name),
            $this->class,
            $this->name,
            $this->attributes["size"],
            $this->readonly ? ' readonly="readonly"' : '',
            $this->disabled ? ' disabled="disabled"' : '',
            $options,
        );
        return vsprintf('<select id="%s" class="%s" name="%s" size="%d"%s%s>%s</select>', $args) . "\n";
    }

    public function setDisabledOption($option) {
        $options = is_array($option) ? $option : explode(",", $option);
        $this->optiondisabled = array_merge($this->optiondisabled, $options);
    }

    public function setReadonlyOption($option) {
        $options = is_array($option) ? $option : explode(",", $option);
        $this->optionreadonly = array_merge($this->optionreadonly, $options);
    }

    public function setDefaultValue($value) {
        $this->defaultValue = is_array($value) ? $value : explode(",", $value);
        return $this;
    }

}
