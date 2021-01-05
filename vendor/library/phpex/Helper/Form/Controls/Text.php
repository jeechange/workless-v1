<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form\Controls;

use phpex\Helper\Form\Form;

/**
 * Description of Text
 *
 * @author Administrator
 */
class Text extends BaseControl {

    public function __construct(Form $form, $name, $label, $maxlength = null) {
        parent::__construct($form, $name, $label);
        $this->class = "frm-text";
        $this->attributes["maxlength"] = $maxlength? : 255;
    }

    protected function generateControl() {

        $args = array(
            $this->form->getWrap()->generateKey($this->name),
            $this->class,
            $this->name,
            $this->attributes["maxlength"],
            $this->defaultValue,
            $this->readonly ? ' readonly="readonly"' : '',
            $this->disabled ? ' disabled="disabled"' : '',
        );
        return vsprintf('<input type="text" id="%s" class="%s"  name="%s" maxlength="%d" value="%s"%s%s>', $args) . "\n";
    }

}
