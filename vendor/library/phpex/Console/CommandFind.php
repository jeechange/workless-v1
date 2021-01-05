<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Console;

use Symfony\Component\Console\Command\Command;

/**
 * Description of CommandFind
 *
 * @author Administrator
 */
class CommandFind {

    protected $commands = array();

    public function find($namespace, $path) {
        $files = glob($path . "/*Command.php");
        if ($files) {
            foreach ($files as $file) {
                $classname = $namespace . "\\Command\\" . rtrim(basename($file), ".php");
                $r = new \ReflectionClass($classname);
                if ($r->isSubclassOf('Symfony\\Component\\Console\\Command\\Command') && !$r->isAbstract() && !$r->getConstructor()->getNumberOfRequiredParameters()) {
                    $this->commands[] = $r->newInstance();
                }
            }
        }
    }

    /**
     * @return Command[]
     */
    public function getCommands() {
        return $this->commands;
    }

}
