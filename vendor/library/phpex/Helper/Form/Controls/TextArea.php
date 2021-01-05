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
class TextArea extends BaseControl {

    public function __construct(Form $form, $name, $label, $cols = NULL, $rows = NULL) {
        parent::__construct($form, $name, $label);
        $this->class = "frm-textarea";
        $this->attributes["cols"] = $cols;
        $this->attributes["rows"] = $rows;
    }

    protected function generateControl() {

        $args = array(
            $this->form->getWrap()->generateKey($this->name),
            $this->class,
            $this->name,
            $this->attributes["cols"]?:60,
            $this->attributes["rows"]?:5,
            $this->readonly ? ' readonly="readonly"' : '',
            $this->disabled ? ' disabled="disabled"' : '',
            $this->defaultValue,
        );
        return vsprintf('<textarea id="%s" class="%s" name="%s" cols="%d" rows="%d" %s%s>%s</textarea>', $args) . "\n";
    }

}
