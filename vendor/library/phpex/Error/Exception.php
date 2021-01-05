<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Error;

use Exception as BaseException;

/**
 * Description of Exception
 *
 * @author Administrator
 */
class Exception extends BaseException {

    /** @var string */
    public $sourceCode;

    /** @var string */
    public $sourceName;

    /** @var int */
    public $sourceLine;

    public function setSource($code, $line, $file = NULL) {
        $this->sourceCode = (string) $code;
        $this->sourceLine = (int) $line;
        $this->sourceName = (string) $file;
        if (is_file($file)) {
            $this->message = rtrim($this->message, '.')
                    . ' in ' . dirtrim($file) . ($line ? ":$line" : '');
        }
        return $this;
    }

}
