<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Util\ORG;

/**
 *
 * @author Administrator
 */
interface SeoParseInterface {

    public function getTitle();
    
    public function getSubTitle();

    public function getKeywords();

    public function getDescription();
}
