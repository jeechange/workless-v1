<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Latte;

/**
 * The exception occured during Latte compilation.
 *
 * @author     David Grudl
 */
class CompileException extends \Exception {

    /** @var string */
    public $sourceCode;

    /** @var string */
    public $sourceName;

    /** @var int */
    public $sourceLine;

    public function setSource($code, $line, $name = NULL) {
        $this->sourceCode = (string) $code;
        $this->sourceLine = (int) $line;
        $this->sourceName = (string) $name;
        if (is_file($name)) {
            $this->message = rtrim($this->message, '.')
                    . ' in ' . str_replace(dirname(dirname($name)), '...', $name) . ($line ? ":$line" : '');
        }
        return $this;
    }

}
