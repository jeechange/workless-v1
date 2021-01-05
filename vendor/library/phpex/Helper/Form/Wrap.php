<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form;

/**
 * Description of Wrap
 *
 * @author Administrator
 */
class Wrap {

    protected $prefix = "frm_%s";
    private $wrappers = array(
        'controls' => 'table',
        'button-controls' => 'table',
        'control' => 'tr',
        'label' => 'th',
        'element' => 'td',
        'item' => 'label',
        'tip' => 'td',
    );
    private $classes = array(
        'controls' => 'frm-controls',
        'button-controls' => 'frm-btn-controls',
        'control' => 'frm-control',
        'label' => 'frm-label',
        'element' => 'frm-element',
        'item' => 'frm-item',
        'tip' => 'frm-tip',
    );
    private $extra = array(
        'controls' => array(),
        'button-controls' => array(),
        'control' => array(),
        'label' => array(),
        'element' => array(),
        'item' => array(),
        'tip' => array(),
    );

    public function setControls($wrap, $class = null, $extra = array()) {
        $this->wrappers['controls'] = $wrap;
        $class and $this->classes['controls'] = $class;
        $this->extra['controls'] = $extra;
        return $this;
    }

    public function setButtonControls($wrap, $class = null, $extra = array()) {
        $this->wrappers['button-controls'] = $wrap;
        $class and $this->classes['button-controls'] = $class;
        $this->extra['button-controls'] = $extra;
        return $this;
    }

    public function setControl($wrap, $class = null, $extra = array()) {
        $this->wrappers['control'] = $wrap;
        $class and $this->classes['control'] = $class;
        $this->extra['control'] = $extra;
        return $this;
    }

    public function setLabel($wrap, $class = null, $extra = array()) {
        $this->wrappers['label'] = $wrap;
        $class and $this->classes['label'] = $class;
        $this->extra['label'] = $extra;
        return $this;
    }

    public function setElement($wrap, $class = null, $extra = array()) {
        $this->wrappers['element'] = $wrap;
        $class and $this->classes['element'] = $class;
        $this->extra['element'] = $extra;
        return $this;
    }

    public function setItem($wrap, $class = null, $extra = array()) {
        $this->wrappers['item'] = $wrap;
        $class and $this->classes['item'] = $class;
        $this->extra['item'] = $extra;
        return $this;
    }

    public function setTip($wrap, $class = null, $extra = array()) {
        $this->wrappers['tip'] = $wrap;
        $class and $this->classes['tip'] = $class;
        $this->extra['tip'] = $extra;
        return $this;
    }

    public function begin($wrap, $name) {
        if ($this->wrappers[$wrap]) {
            $wrappers = array(
                $this->wrappers[$wrap],
                $wrap,
                $this->generateKey($name),
                $this->classes[$wrap],
                $this->extra[$wrap] ? StrFilter::attributes($this->extra[$wrap]) : ""
            );
            return vsprintf('<%s id="%s_%s" class="%s"%s>', $wrappers) . "\n";
        }
        return "";
    }

    public function end($wrap) {
        if ($this->wrappers[$wrap])
            return "</" . $this->wrappers[$wrap] . ">\n";
        return "";
    }

    public function generateKey($name) {
        return sprintf($this->prefix, $name);
    }

}
