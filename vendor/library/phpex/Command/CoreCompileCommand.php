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

/**
 * Description of CoreCompileCommand
 *
 * @author river2liu <river2liu@jeechange.com>
 */
class CoreCompileCommand extends Command {

    private $export = array();
    private $paths = array(
        array("/library/phpex/Library/*.php", "phpex\Library"),
        array("/library/phpex/Loader/*.php", "phpex\Loader"),
        array("/library/phpex/Access/*.php", "phpex\Access"),
        array("/library/phpex/Asset/Asset.php", "phpex\Asset"),
        array("/library/phpex/Console/*.php", "phpex\Console"),
        array("/library/phpex/DModel/*.php", "phpex\DModel"),
        array("/library/phpex/Driver/*.php", "phpex\Driver"),
        array("/library/phpex/Error/*.php", "phpex\Error"),
        array("/library/phpex/Event/*.php", "phpex\Event"),
        array("/library/phpex/Foundation/*.php", "phpex\Foundation"),
        array("/library/phpex/Foundation/File/*.php", "phpex\Foundation\File"),
        array("/library/phpex/Foundation/File/Exception/*.php", "phpex\Foundation\File\Exception"),
        array("/library/phpex/Foundation/File/MimeType/*.php", "phpex\Foundation\File\MimeType"),
        array("/library/phpex/Helper/Search/*.php", "phpex\Helper\Search"),
        array("/library/phpex/Util/Xml/*.php", "phpex\Util\Xml"),
        array("/library/phpex/Util/ORG/*.php", "phpex\Util\ORG"),
        array("/library/phpex/Util/Yaml/*.php", "phpex\Util\Yaml"),
        array("/engine/Engine/*.php", "Engine"),
        array("/engine/Latte/Latte/*.php", "Latte"),
        array("/engine/Latte/Latte/Loaders/*.php", "Latte\Loaders"),
        array("/engine/Latte/Latte/Macros/*.php", "Latte\Macros"),
        array("/engine/Latte/Latte/Runtime/*.php", "Latte\Runtime"),
        array("/psr/log/Psr/Log/*.php", "Psr\Log"),
        array("monolog/monolog/src/Monolog/*.php", "Monolog"),
        array("monolog/monolog/src/Monolog/Formatter/LineFormatter.php", "Monolog\\Formatter"),
        array("monolog/monolog/src/Monolog/Handler/StreamHandler.php", "Monolog\\Handler"),
        array("monolog/monolog/src/Monolog/Handler/AbstractProcessingHandler.php", "Monolog\\Handler"),
        array("monolog/monolog/src/Monolog/Handler/AbstractHandler.php", "Monolog\\Handler"),
        array("monolog/monolog/src/Monolog/Handler/HandlerInterface.php", "Monolog\\Handler"),
    );

    protected function configure() {
        $this->setName('Core:compile')
                ->addOption('compress', null, InputOption::VALUE_NONE, '')
                ->setDescription('Core compile')
                ->setHelp("Core compile");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $corecachepath = main()->getAppRoot() . "/core.php.cache";
        $compress = $input->getOption('compress');
        $handle = fopen($corecachepath, "a+");
        if (!$handle) {
            $output->writeln(sprintf("<error>Unable to open or create file '%s' </error>", $corecachepath));
            return;
        }
        ftruncate($handle, 0);
        fwrite($handle, "<?php\n");
        $content = "";
        foreach ($this->paths as $path) {
            $files = glob(main()->getVendorRoot() . "/" . $path[0]);
            if ($files) {
                foreach ($files as $file) {
                    if (false !== strpos($file, "Trait.php")) {
                        continue;
                    }
                    $class = $path[1] . "\\" . substr(basename($file), 0, -4);
                    $content.=$this->export($class, $output, $compress);
                }
            }
        }
        fwrite($handle, preg_replace("/[\r\n]{2,}/", "\n", $content));
        $output->writeln(sprintf("write file=><comment>%s</comment>", $corecachepath));
    }

    function export($class, $output, $compress) {
        if (isset($this->export[$class])) {
            return "";
        }
        $export = "";
        $ref = new \ReflectionClass($class);
        if (!$ref->isUserDefined())
            return "";
        $parent = $ref->getParentClass();
        if ($parent) {
            $export = $this->export($parent->name, $output, $compress) . $export;
        }
        $interfaceies = $ref->getInterfaceNames();
        if ($interfaceies) {
            foreach ($interfaceies as $interface) {
                $export = $this->export($interface, $output, $compress) . $export;
            }
        }
        $this->export[$class] = true;
        $contents = file_get_contents($ref->getFileName());
        if (substr($contents, 0, 1) != "<") {
            $output->writeln(sprintf("file Exception =><error>%s</error>", $class));
        }
        $output->writeln(sprintf("write file=><comment>%s</comment>", $class));

        return $export . $this->compile($ref->getFileName(), $compress) . "\n";
    }

    public function compile($filename, $compress) {
        $content = $this->strip_whitespace($filename, $compress);
        $content = trim(substr($content, 5));
        // 替换预编译指令
        // $content = preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s', '', $content);
        if (0 === strpos($content, 'namespace')) {
            $content = preg_replace('/namespace\s(.*?);/', 'namespace \\1{', $content, 1);
        } else {
            $content = 'namespace {' . $content;
        }
        if ('?>' == substr($content, -2))
            $content = substr($content, 0, -2);
        return $content . "\n}\n";
    }

    function strip_whitespace($filename, $compress) {
        $content = file_get_contents($filename);
        $stripStr = '';
        //分析php源码
        $tokens = token_get_all($content);
        $last_space = false;
        for ($i = 0, $j = count($tokens); $i < $j; $i++) {
            if (is_string($tokens[$i])) {
                $last_space = false;
                $stripStr .= $tokens[$i];
            } else {
                switch ($tokens[$i][0]) {
                    //过滤各种PHP注释
                    case T_COMMENT:
                    case T_DOC_COMMENT:
                        break;
                    case T_WHITESPACE:
                        if (!$last_space && $compress) {
                            $stripStr .= ' ';
                            $last_space = true;
                        } else {
                            $last_space = false;
                            $stripStr .= $tokens[$i][1];
                        }
                        break;
                    default:
                        $last_space = false;
                        $stripStr .= $tokens[$i][1];
                }
            }
        }
        return $stripStr;
    }

}
