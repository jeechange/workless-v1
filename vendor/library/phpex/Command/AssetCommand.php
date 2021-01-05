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
use phpex\Asset\JSMin;
use phpex\Asset\SassPrepare;
use phpex\Asset\LessPrepare;

class AssetCommand extends Command {

    protected $error;
    protected $currentDir = "";
    protected $verdorPath = "";
    protected $appDir = "";
    protected $config = array();
    protected $copy3 = array();
    protected $copy4 = array(".jpg", ".gif", ".png", ".bmp", ".avi", ".swf", ".doc", ".htm", '.mp3', ".pdf", '.ppt', ".tif", ".xsl",".htc",".eot",".ttf",".svg");
    protected $copy5 = array(".html", ".jpeg", ".mpeg",".woff");

    protected function configure() {
        $this->setName('Asset:install')
                ->setDescription('Asset compile')
                ->addOption('compress', null, InputOption::VALUE_NONE, '')
                ->addOption('filter', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_NONE, 'A string pattern used to match entities that should be processed.')
                ->addOption("theme", null, InputOption::VALUE_REQUIRED | InputOption::VALUE_NONE, "default theme")
                ->setHelp("Asset compile");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        /* @var $apps \phpex\Library\AppInterface[] */
        $apps = ins()->getTag("app");
        $this->config = $config = C("asset");
        $this->appDir = main()->getAppRoot();
        $this->verdorPath = $verdorPath = main()->getVendorRoot() . "/asset";
        $compress = $input->getOption('compress');
        $basePath = Q()->server->get("SCRIPT_FILENAME");
        $basePath = dirname(dirname($basePath)) . "/www/" . $config["basedir"];
        $basePath = str_replace("\\", "/", $basePath);
        $verdorPath = str_replace("\\", "/", $verdorPath);
        $this->currentDir = $verdorPath;

        $filter = lcfirst($input->getOption('filter'));
        if (!$filter || $filter == "core") {
            if ($compress) {
                $this->compress($this->currentDir, $basePath . "/core");
            } else {
                $this->compile($this->currentDir, $basePath . "/core");
            }
            if (!$this->error) {
                $output->writeln("Installing assets for <comment>" . $verdorPath . "</comment> => <comment>" . $basePath . "/core</comment>");
            } else {
                $output->writeln("<error>" . $this->error . "</error>");
                return;
            }
        } else {
            $output->writeln("<comment>core ignore</comment>");
        }
        $theme = lcfirst($input->getOption('theme'));

        foreach ($apps as $app) {

            $t = $theme? : $app->getTheme();
            $dir = $app->getPublicName() . rtrim("/" . $t, "/");
            $this->currentDir = $app->getRoot() . "/View/" . ($t ? $t . "/Public" : "Public");

            if ($filter && $app->getName() != $filter) {
                $output->writeln("<comment>" . $app->getName() . " ignore</comment>");
                continue;
            }

            if ($compress) {
                $this->compress($this->currentDir, $basePath . "/" . $dir);
            } else {
                $this->compile($this->currentDir, $basePath . "/" . $dir);
            }
            if (!$this->error) {
                $output->writeln("Installing assets for <comment>" . str_replace("\\", "/", $this->currentDir) . "</comment> => <comment>" . $basePath . "/" . $dir . "</comment>");
            } else {
                $output->writeln("<error>" . $this->error . "</error>");
                return;
            }
        }
        $output->writeln($compress ? "asset compress install is finish" : "asset install is  finish");
    }

    protected function compress($src, $target) {
        $dir_handle = opendir($src);
        if (!is_dir($target) && !mkdir($target, 777, true)) {
            $this->error = "Directory to create failure:" . $target;
            return false;
        }
        if ($dir_handle) {
            while (false !== ($file = readdir($dir_handle))) {
                if ("." == $file[0] || "_" == $file[0])
                    continue;
                $path = $src . "/" . $file;
                if (is_dir($path)) {
                    $exec = $this->compress($path, $target . "/" . $file);
                    if (!$exec)
                        return false;
                    continue;
                }
                if ('.inc.js' == substr($file, -7)) {
                    $content = $this->importjs($path);
                    if (false === $content)
                        return false;
                    $content = JSMin::minify($content);
                    $targetfile = $target . "/" . substr($file, 0, -7) . ".js";
                    file_put_contents($targetfile, $content);
                } elseif ('.inc.css' == substr($file, -8)) {
                    $content = $this->importcss($path);
                    if (false === $content)
                        return false;
                    $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
                    $content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);
                    $targetfile = $target . "/" . substr($file, 0, -8) . ".css";
                    file_put_contents($targetfile, $content);
                } elseif ('.scss' == substr($file, -5)) {
                    $Sass = new SassPrepare($path);
                    $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $Sass->getString());
                    $content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);
                    $targetfile = $target . "/" . substr($file, 0, -5) . ".css";
                    file_put_contents($targetfile, $content);
                } elseif ('.less' == substr($file, -5)) {
                    $Less = new LessPrepare($path);
                    $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $Less->getString());
                    $content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);
                    $targetfile = $target . "/" . substr($file, 0, -5) . ".css";
                    file_put_contents($targetfile, $content);
                } elseif (in_array(substr($file, -4), $this->copy4) || in_array(substr($file, -5), $this->copy5)) {
                    $targetfile = $target . "/" . $file;
                    if (is_file($targetfile)) {
                        unlink($targetfile);
                    }
                    if (!copy($path, $targetfile)) {
                        $this->error = $path . " copy to " . $targetfile . " failure";
                        return false;
                    }
                } elseif ('.js' == substr($file, -3)) {
                    $content = file_get_contents($path);
                    $content = JSMin::minify($content);
                    $targetfile = $target . "/" . $file;
                    file_put_contents($targetfile, $content);
                } elseif ('.css' == substr($file, -4)) {
                    $content = file_get_contents($path);
                    $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
                    $content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);
                    $targetfile = $target . "/" . $file;
                    file_put_contents($targetfile, $content);
                }
            }
        }
        closedir($dir_handle);
        return true;
    }

    protected function compile($src, $target) {
        $dir_handle = opendir($src);
        if (!is_dir($target) && !mkdir($target, 777, true)) {
            $this->error = "Directory to create failure:" . $target;
            return false;
        }
        if ($dir_handle) {
            while (false !== ($file = readdir($dir_handle))) {
                if ("." == $file[0] || "_" == $file[0])
                    continue;
                $path = $src . "/" . $file;
                if (is_dir($path)) {
                    $exec = $this->compile($path, $target . "/" . $file);
                    if (!$exec)
                        return false;
                    continue;
                }
                if ('.inc.js' == substr($file, -7)) {
                    $content = $this->importjs($path);
                    if (false === $content)
                        return false;
                    $targetfile = $target . "/" . substr($file, 0, -7) . ".js";
                    file_put_contents($targetfile, $content);
                } elseif ('.inc.css' == substr($file, -8)) {
                    $content = $this->importcss($path);
                    if (false === $content)
                        return false;
                    $targetfile = $target . "/" . substr($file, 0, -8) . ".css";
                    file_put_contents($targetfile, $content);
                } elseif ('.scss' == substr($file, -5)) {
                    $Sass = new SassPrepare($path);
                    $targetfile = $target . "/" . substr($file, 0, -5) . ".css";
                    file_put_contents($targetfile, $Sass->getString());
                } elseif ('.less' == substr($file, -5)) {
                    $Less = new LessPrepare($path);
                    $targetfile = $target . "/" . substr($file, 0, -5) . ".css";
                    file_put_contents($targetfile, $Less->getString());
                } elseif (in_array(substr($file, -4), $this->copy4) || in_array(substr($file, -5), $this->copy5)) {
                    $targetfile = $target . "/" . $file;
                    if (is_file($targetfile)) {
                        unlink($targetfile);
                    }
                    if (!copy($path, $targetfile)) {
                        $this->error = $path . " copy to " . $targetfile . " failure";
                        return false;
                    }
                } elseif ('.js' == substr($file, -3) || '.css' == substr($file, -4)) {
                    $targetfile = $target . "/" . $file;
                    if (is_file($targetfile)) {
                        unlink($targetfile);
                    }
                    if (!copy($path, $targetfile)) {
                        $this->error = $path . " copy to " . $targetfile . " failure";
                        return false;
                    }
                }
            }
        }
        closedir($dir_handle);
        return true;
    }

    protected function importjs($path) {
        $content = file_get_contents($path);
        $lists = explode("//=", trim($content));
        $lists = array_filter($lists);
        $lists = array_unique($lists);
        $jscontent = "";
        foreach ($lists as $list) {
            $list = trim($list);
            if (empty($list))
                continue;
            if (is_file($list)) {
                $file = $list;
            } elseif ("./" == substr($list, 0, 2)) {
                $file = dirname($path) . substr($list, 1);
            } elseif ("-/" == substr($list, 0, 2)) {
                $file = $this->verdorPath . substr($list, 1);
            } elseif ("/" == $list[0]) {
                $file = $this->currentDir . "/" . $list;
            } else {
                $file = $this->appDir . "/" . $this->modulePath($list);
            }
            if (!is_file($file)) {
                $this->error = "File does not exist in:" . $path . " for:" . $list;
                return FALSE;
            }
            if (".inc.js" == strtolower(substr($file, -7))) {
                $this->error = "Doesn't support nested references in:" . $path . " for:" . $list;
                return FALSE;
            }
            if (".js" != strtolower(substr($file, -3))) {
                $this->error = "References must be js file in:" . $path . " for:" . $list;
                return FALSE;
            }
            $jscontent.="\n\n" . file_get_contents($file);
        }
        return $jscontent;
    }

    protected function importcss($path) {
        $content = file_get_contents($path);
        $lists = explode("//=", trim($content));
        $lists = array_filter($lists);
        $lists = array_unique($lists);
        $csscontent = "";
        foreach ($lists as $list) {
            $list = trim($list);
            if (empty($list))
                continue;
            if (is_file($list)) {
                $file = $list;
            } elseif ("./" == substr($list, 0, 2)) {
                $file = dirname($path) . substr($list, 1);
            } elseif ("-/" == substr($list, 0, 2)) {
                $file = $this->verdorPath . substr($list, 1);
            } elseif ("/" == $list[0]) {
                $file = $this->currentDir . "/" . $list;
            } else {
                $file = $this->appDir . "/" . $this->modulePath($list);
            }
            if (!is_file($file)) {
                $this->error = "File does not exist in:" . $path . " for:" . $list;
                return FALSE;
            }
            if (".inc.css" == strtolower(substr($file, -7))) {
                $this->error = "Doesn't support nested references in:" . $path . " for:" . $list;
                return FALSE;
            }
            if (".less" == strtolower(substr($file, -5))) {
                $Less = new LessPrepare($file);
                $csscontent.=$Less->getString();
                continue;
            }
            if (".scss" == strtolower(substr($file, -5))) {
                $Scss = new SassPrepare($file);
                $csscontent.=$Scss->getString();
                continue;
            }
            if (".css" != strtolower(substr($file, -4))) {
                $this->error = "References must be css or scss or less file in:" . $path . " for:" . $list;
                return FALSE;
            }
            $csscontent.="\n\n" . file_get_contents($file);
        }
        return $csscontent;
    }

    public function modulePath($path) {
        $keys = array_keys($this->config["folder"]);
        foreach ($keys as $key => $val) {
            $keys[$key] = '{' . $val . '}';
        }
        $path = str_ireplace($keys, $this->config["folder"], $path);
        return ltrim($path, "/");
    }

}
