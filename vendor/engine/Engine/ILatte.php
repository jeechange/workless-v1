<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Engine;

use Latte\IMacro;

/**
 * Description of IMacro
 *
 * @author Administrator
 */
interface ILatte extends IMacro {

    public static function installExtend(\Latte\Engine $latte);
}
