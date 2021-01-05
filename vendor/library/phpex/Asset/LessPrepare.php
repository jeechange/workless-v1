<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace phpex\Asset;
/**
 * Description of SassPrepare
 *
 * @author Administrator
 */
require_once __DIR__.'/Lessc.php';
class LessPrepare {

    private $tostring,   $path;

    public function __construct($path) {        
        $this->tostring = is_file($path) ? file_get_contents($path) : "";
        $this->path=$path;
    }    
    public function getString(){
        $less = new \lessc;
        $less->addImportDir(dirname($this->path)."/");
        $tring = $less->compile($this->tostring);       
        return $tring;
    }

}