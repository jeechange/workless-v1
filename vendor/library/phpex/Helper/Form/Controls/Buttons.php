<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form\Controls;

/**
 * Description of Buttons
 *
 * @author Administrator
 */
class Buttons extends BaseControl {

    protected $buttons = array();

    protected function generateControl() {
        $controls = "";
        foreach ($this->buttons as $btn) {
            $controls.= $btn->renderControl();
        }
        return $controls;
    }

    /**
     * 添加一个按钮
     * @param string $caption
     * @return Button
     */
    public function addButton($caption) {
        return $this->buttons[] = new Button($this->form, $this->name, $caption, "button");
    }

    /**
     * 添加一个提交按钮
     * @param string $caption
     * @return Button
     */
    public function addSubmit($caption) {
        return $this->buttons[] = new Button($this->form, $this->name, $caption, "submit");
    }

    /**
     * 添加一天重置按钮
     * @param string $caption
     * @return Button
     */
    public function addResetButton($caption) {
        return $this->buttons[] = new Button($this->form, $this->name, $caption, "reset");
    }

    public function renderBegin() {
        return $this->form->getWrap()->begin("button-controls", $this->name) . parent::renderBegin();
    }

    public function renderEnd() {
        return parent::renderEnd() . $this->form->getWrap()->end("button-controls");
    }

    public function renderControl() {
        return $this->generateControl();
    }

    public function generateLabel() {
        return "";
    }

}
