<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

/**
 * Description of ClearCommand
 *
 * @author Administrator
 */
class ClearCommand extends Command {

    protected $delFile = 0;
    protected $delDir = 0;
    private $error;
    private $ignore;

    protected function configure() {
        $this->setName("cache:clear");
    }

    /**
     * 
     * @return DialogHelper
     */
    protected function getDialogHelper() {
        if ($this->getHelperSet()->has('dialog')) {
            $dialog = $this->getHelperSet()->get('dialog');
        } else {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }
        return $dialog;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $dialog = $this->getDialogHelper();
        do {
            $ignore = $dialog->ask($output, "ignore session [<info>n</info>]", "n");
            $ignore = strtolower($ignore);
        } while (!in_array($ignore, array("y", "yes", "n", "no")));
        $this->ignore=$ignore = in_array($ignore, array("y", "yes")) ? true : false;
        $cache = \app()->getAppRoot() . "/cache" ;
        $dir_handle = opendir($cache);
        if ($dir_handle) {            
            while (false !== ($file = readdir($dir_handle))) {
                if (in_array($file, $ignorelist)) {
                    continue;
                }
                $path = $cache . "/" . $file;
                is_dir($path) ? $this->deleteDir($path) : $this->deleteFile($path);
            }
        }
        closedir($dir_handle);
        if ($this->error) {
            $output->writeln(sprintf("<error>%s</error>", $this->error));
            return 1;
        }
        if ($ignore) {
            $output->writeln("Clearing the cache for the <info>" . \app()->getEnv() . "</info> environment <info>true</info>,<comment>ignore session </comment> ");
        } else {
            $output->writeln("Clearing the cache for the <info>" . \app()->getEnv() . "</info> environment <info>true</info>");
        }
        $output->writeln("Clearing directory <info>" . $this->delDir . "</info> ");
        $output->writeln("Clearing file <info>" . $this->delFile . "</info> ");
    }

    public function deleteDir($path) {  
        $ignorelist = $this->ignore ? array(".", "..", "session") : array(".", "..", "session");
        if (is_file($path)) {
            return $this->deleteFile($path);
        }       
        $h = opendir($path);       
        if ($h) {
            while (false !== ($file = readdir($h))) {
                if (in_array($file, $ignorelist)) {
                    continue;
                }
                $path1 = $path . "/" . $file;
                if (is_dir($path1)) {
                    $del = $this->deleteDir($path1);
                    if (!$del) {
                        return false;
                    }
                    if (!rmdir($path1)) {
                        $this->error = sprintf("Deleting directory error :'%s'", $path);
                        return false;
                    }
                    $this->delDir++;
                } else {
                    $del = $this->deleteFile($path1);
                    if (!$del) {
                        return false;
                    }
                }
            }
        }

        closedir($h);
        if (!rmdir($path)) {
            $this->error = sprintf("Deleting directory error :'%s'", $path);
            return false;
        }
        $this->delDir++;
        return true;
    }

    public function deleteFile($path) {
        if (is_dir($path)) {
            return $this->deleteDir($path);
        }
        if (file_exists($path)) {
            if (!unlink($path)) {
                $this->error = sprintf("Deleting files error :'%s'", $path);
                return false;
            }
            $this->delFile++;
        }
        return true;
    }

}
