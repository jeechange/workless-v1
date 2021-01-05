<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Loader;

/**
 * Description of LoaderInterface
 *
 * @author Administrator
 */
interface LoaderInterface {

    public function loadClass($class);

    public function register($prepend = false);

    public function unregister();
}
