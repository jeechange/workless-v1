<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Error;

/**
 * Description of ErrorHandler
 *
 * @author Administrator
 */
class ErrorHandler {

    public function register_shutdown_function($callable) {        
        if (is_callable($callable)) {
            register_shutdown_function($callable);
        }       
    }

    public function set_error_handler($callable) {
        if (is_callable($callable)) {
            set_error_handler($callable);
        }
    }

    public function set_exception_handler($callable) {
        if (is_callable($callable)) {
            set_exception_handler($callable);
        }
    }

}
