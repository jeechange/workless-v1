<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Search;

/**
 *
 * @author Administrator
 */
interface ElementInterface {

    public function __construct($field, $label);

    public function bindSearch(Search $search);

    public function getName();

    public function getExpr();

    public function setHtmlAttr(array $attrs = array());

    public function setParameters(array &$arameters);

    public function getHtml();

    public function getValue();
}
