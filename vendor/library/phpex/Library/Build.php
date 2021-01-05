<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Library;

/**
 * Description of Build
 *
 * @author Administrator
 */
class Build {

    protected static $directorys = array(
        "/Common",
        "/Conf",
        "/Controller",
        "/Event",
        "/Service",
        "/View",
        "/View/#theme#",
        "/View/#theme#Index",
        "/View/#theme#Public",
    );
    protected static $files = array(
        "/Common/function.php" => "createFunction",
        "/Conf/access.yml" => "createAccess",
        "/Conf/config.yml" => "createConf",
        "/Conf/route.yml" => "createRoute",
        "/Conf/role.yml" => "createRole",
        "/Controller/CommonController.php" => "createCommonController",
        "/Controller/IndexController.php" => "createIndexController",
        "/View/#theme#/Index/index.latte" => "createIndexLatte",
    );
    protected static $appTemplate = <<<app
<?php

namespace #App#;

use phpex\Library\App;

class #App#App extends App {
            
    public function getName() {
        return "#app#";
    }
}
app;
    protected static $IndexControllerTemplate = <<<late
<?php

namespace #App#\Controller;
            
class IndexController extends CommonController{
            
    public function index() { 
        //控制器示例代码
        return \$this->display();
    }
}
late;
    protected static $CommonControllerTemplate = <<<late
<?php

namespace #App#\Controller;
use phpex\Library\Controller;  
    
abstract class CommonController extends Controller{
            
    protected function _initialize() {
        
    }  
}
late;
    protected static $IndexLatteTemplate = <<<late
<style type="text/css">
    *{ padding: 0; margin: 0; } 
    div{ padding: 4px 48px;} 
    body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} 
    h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } 
    p{ line-height: 1.8em; font-size: 36px }
</style>
<div style="padding: 24px 48px;"> 
    <h1>^_^</h1>
    <p>您现在访问的是[#App#]模块下[IndexController]控制器中的[index]方法</p>
    <p>欢迎使用 <b>phpex</b>！</p><br/>
</div>
late;
    protected static $RouteTemplate = <<<late
#app#_index_index:
    pattern: /
    callback: "#App#:Index:index"    
    method: get   
    options:
late;

    static function buildAppList(MainInterface $main) {
        $appLists = $main->getAppList();
        $config = C();
        $ins = ins();
        foreach ($appLists as $name) {
            /* @var $app AppInterface */
            $app = ($main->getEnv() == "dev") ? self::createApp($name, $main) : self::newApp($name, $main);
            $app->loadConfig($config);
            \Composer\Autoload\includeFile($app->getRoot() . "/Common/function.php");
            $app->installInstance($ins);
            $ins->addInstance("app.$name", $app, array("app"));
        }
    }

    static protected function createApp($name, MainInterface $main) {
        $appClass = "$name\\{$name}App";
        $pathRoot = $main->getAppRoot() . "/$name";
        if (!class_exists($appClass)) {
            $path = "{$pathRoot}/{$name}App.php";
            if (!is_dir($pathRoot) && !mkdir($pathRoot, 777, true)) {
                E("Unable to create directory '%s'", $pathRoot);
            }
            if (!is_file($path)) {
                $hand = fopen($path, "c+");
                flock($hand, LOCK_SH);
                if (!$hand) {
                    E("Unable to open or create app '%s'", $appClass);
                } else {
                    ftruncate($hand, 0);
                    flock($hand, LOCK_EX);
                    $code = str_replace(array("#App#", "#app#"), array($name, lcfirst($name)), self::$appTemplate);
                    if (fwrite($hand, $code, strlen($code)) !== strlen($code)) {
                        ftruncate($hand, 0);
                        E("Unable to create app '%s'", $appClass);
                    }
                    flock($hand, LOCK_SH);
                    fclose($hand);
                }
            }
            include $path;
        }
        /* @var $app AppInterface */
        $app = new $appClass();
        if (!$app->isAutoBuild()) {
            return $app;
        }
        $theme = $app->getTheme() ? $app->getTheme() . "/" : "";
        foreach (self::$directorys as $directory) {
            $directoryPath = $pathRoot . str_replace(array("#theme#"), array($theme), $directory);
            if (!is_dir($directoryPath) && !mkdir($directoryPath, 0777, true)) {
                E("Unable to create directory '%s'", $directoryPath);
            }
        }

        foreach (self::$files as $file => $create) {
            $filePath = $pathRoot . str_replace(array("#theme#"), array($theme), $file);
            if (is_file($filePath)) {
                continue;
            }
            $fhand = fopen($filePath, "c+");
            if (!$fhand) {
                E("Unable to open or create file '%s'", $filePath);
            }
            flock($fhand, LOCK_SH);
            if (filesize($filePath) == 0) {
                ftruncate($fhand, 0);
                flock($fhand, LOCK_EX);
                $code = self::$create($name);
                if (fwrite($fhand, $code, strlen($code)) !== strlen($code)) {
                    E("Unable to create file '%s'", $filePath);
                }
                flock($fhand, LOCK_SH);
            }
            fclose($fhand);
        }
        return $app;
    }

    static protected function newApp($name, MainInterface $main) {
        $appClass = "$name\\{$name}App";
        if (!class_exists($appClass)) {
            E("Unable to create app '%s'", $appClass);
        }
        return new $appClass();
    }

    static protected function createFunction($name) {
        return "<?php\n//$name function";
    }

    static protected function createAccess($name) {
        return "#//$name Access";
    }

    static protected function createConf($name) {
        return "#//$name Conf";
    }

    static protected function createRoute($name) {
        return "#//$name route\n" . str_replace(array("#App#", "#app#"), array($name, lcfirst($name)), self::$RouteTemplate);
    }

    static protected function createRole($name) {
        return "#//$name role\n";
    }

    static protected function createCommonController($name) {
        return str_replace(array("#App#", "#app#"), array($name, lcfirst($name)), self::$CommonControllerTemplate);
    }

    static protected function createIndexController($name) {
        return str_replace(array("#App#", "#app#"), array($name, lcfirst($name)), self::$IndexControllerTemplate);
    }

    static protected function createIndexLatte($name) {
        return str_replace(array("#App#", "#app#"), array($name, lcfirst($name)), self::$IndexLatteTemplate);
    }

}
