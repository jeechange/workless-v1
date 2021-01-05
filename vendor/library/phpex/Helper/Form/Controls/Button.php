<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form\Controls;

use phpex\Helper\Form\Form;

/**
 * Description of Button
 *
 * @author Administrator
 */
class Button extends BaseControl {

    protected $buttonType = "button";

    public function __construct(Form $form, $name, $label, $buttonType = "button") {
        parent::__construct($form, $name, $label);
        $this->class = "frm-btn";
        $this->buttonType = in_array($buttonType, array("button", "submit", "reset")) ? $buttonType : "button";
    }

    protected function generateControl() {
        $args = array(
            $this->buttonType,
            $this->form->getWrap()->generateKey($this->buttonType . "_" . $this->name),
            $this->class,
            $this->buttonType . "_" . $this->name,
            $this->label,
            $this->readonly ? ' readonly="readonly"' : '',
            $this->disabled ? ' disabled="disabled"' : '',
        );
        return vsprintf('<input type="%s" id="%s" class="%s"  name="%s" value="%s"%s%s>', $args) . "\n";
    }

}
