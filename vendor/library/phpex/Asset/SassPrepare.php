<?php

namespace phpex\Asset;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once __DIR__.'/Scss.php';
/**
 * Description of SassPrepare
 *
 * @author Administrator
 */
class SassPrepare {

    private $tostring,   $path;

    public function __construct($path) {        
        $this->tostring = is_file($path) ? file_get_contents($path) : "";
        $this->path=$path;
    }

    public function getString() {
        $scss = new \scssc();        
        $scss->addImportPath(dirname($this->path)."/");
        $tring = $scss->compile($this->tostring);       
        return $tring;
    }

}
