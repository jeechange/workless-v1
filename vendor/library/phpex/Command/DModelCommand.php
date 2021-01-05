<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DModelCommand extends Command {

    protected $DModelTemplate = <<<DModel
<?php

namespace #App#\DModel;

use phpex\DModel\DModel;

class #entity#DModel extends DModel {
            
    /**
     * 自动填充规则
     */
    public function _fill() {
        //\$this->addFill("pwd", "sysmd5", self::FILL_FUNCTION, self::TYPE_INSERT);  //自动填充示例
    }
    
            
   /**
     * 自动验证规则
     */
    public function _check() {
        //\$this->addRule("names", self::RULE_UNIQUE, "名称必须唯一", "", self::CHECK_NEED, self::TYPE_BOTH);//自动验证示例       
    }
            
    protected function resolveArray(&\$result) {
        
    }
            
    protected function resolveObject(\$result = null) {
        
    }
            
    public function newEntity() {
        return new \#App#\Entity\#entity#();
    }
            
            
}
DModel;

    protected function configure() {
        $this->setName('DModel:generate')
                ->setDescription('Automatically generate DModel')
                ->setHelp("Automatically generate DModel");
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $databases = C("database");
        if (!$databases) {
            $output->writeln("<error>Database configuration information was not found</error>");
            return;
        }
        foreach ($databases as $key => $database) {
            $paths = $database["paths"];
            if (!$paths) {
                $output->writeln(sprintf("<error>The database configuration path does not exist:'%s'</error>", $key));
                continue;
            }
            foreach ($paths as $path) {
                $basePath = dirname(dirname($path));
                if (!is_dir($basePath) && !mkdir($basePath, 777, true)) {
                    $output->writeln(sprintf("Directory to create failure :<error>'%s'</error>", $basePath));
                    return false;
                }
                $namespace = basename($basePath);
                $globs = glob($path . "/*.yml");
                foreach ($globs as $file) {
                    $entity = substr(basename($file), 0, -4);
                    $generatePath = $basePath . "/DModel/" . $entity . "DModel.php";
                    if (is_file($generatePath)) {
                        $output->writeln(sprintf("skip class:<comment>'%s\\DModel\\%sDModel'</comment>", $namespace, $entity));
                    } else {
                        $content = str_replace(array("#App#", "#entity#"), array($namespace, $entity), $this->DModelTemplate);
                        file_put_contents($generatePath, $content);
                        $output->writeln(sprintf("generate class:<info>'%s\\DModel\\%sDModel'</info>", $namespace, $entity));
                    }
                }
            }
        }
    }

}
