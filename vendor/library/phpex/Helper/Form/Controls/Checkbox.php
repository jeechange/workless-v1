<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form\Controls;

use phpex\Helper\Form\Form;

/**
 * Description of Checkbox
 *
 * @author Administrator
 */
class Checkbox extends BaseControl {

    protected $items = array();
    protected $defaultValue = array();
    protected $itemreadonly = array();
    protected $itmedisabled = array();

    public function __construct(Form $form, $name, $label, array $items = array()) {
        parent::__construct($form, $name, $label);
        $this->class = "frm-checkbox";
        $this->items = $items;
    }

    protected function generateControl() {
        $items = "\n";
        foreach ($this->items as $val => $label) {
            $itemargs = array(
                $this->form->getWrap()->generateKey($this->name . "_item_$val"),
                $val,
                in_array($val, $this->itemreadonly) ? ' readonly="readonly"' : '',
                in_array($val, $this->itmedisabled) ? ' disabled="disabled"' : '',
                $label,
                $this->class,
                $this->name,
                in_array($val, $this->defaultValue) ? ' checked="checked"' : ''
            );
            $items.=vsprintf('<input type="checkbox" id="%1$s" phpex-item="%2$s" class="%6$s" name="%7$s" value="%2$s"%3$s%4$s%8$s/><label class="%6$s_item" for="%1$s">%5$s</label>', $itemargs) . "\n";
        }
        $args = array(
            $this->form->getWrap()->generateKey($this->name),
            $this->class,
            $items,
        );
        return vsprintf('<fieldset id="%s" class="%s">%s</fieldset>', $args) . "\n";
    }

    public function setDisabledItem($item) {
        $items = is_array($item) ? $item : explode(",", $item);
        $this->itmedisabled = array_merge($this->itmedisabled, $items);
    }

    public function setReadonlyItem($item) {
        $items = is_array($item) ? $item : explode(",", $item);
        $this->itemreadonly = array_merge($this->itemreadonly, $items);
    }

    public function setDefaultValue($value) {
        $this->defaultValue = is_array($value) ? $value : explode(",", $value);
        return $this;
    }

}
