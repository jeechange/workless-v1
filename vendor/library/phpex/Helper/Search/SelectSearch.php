<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Search;

/**
 * Description of SelectSearch
 *
 * @author Administrator
 */
class SelectSearch implements ElementInterface {

    protected $field, $label, $name, $search;

    public function __construct($field, $label, $name = "") {
        $this->field = $field;
        $this->label = $label;
        $this->name = $name ? str_replace(array(",", "."), array("_", ""), $name) : str_replace(array(",", "."), array("_", ""), $field);
    }

    public function getExpr() {
        
    }

    public function getHtml() {
        
    }

    public function getName() {
        return $this->name;
    }

    public function getValue() {
        
    }

    public function setHtmlAttr(array $attrs = array()) {
        
    }

    public function setParameters(array &$arameters) {
        
    }

    public function bindSearch(Search $search) {
        $this->search = $search;
    }

}
