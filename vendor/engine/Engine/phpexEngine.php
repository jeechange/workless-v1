<?php
/**
 * Created by PhpStorm.
 * User: 28613
 * Date: 2016/3/29
 * Time: 10:11
 */

namespace Engine;

use Latte\Macros\MacroSet;
use Latte\PhpWriter;
use Latte\MacroNode;
//use phpex\Library\Main;
//use Slim\Http\Response;


class phpexEngine extends MacroSet implements ILatte {


    protected $pattern = "/^(?<app>[a-zA-Z0-9_]+@)?(?<theme>[a-zA-Z0-9_]+#)?(?<con>[a-zA-Z0-9_]+[\\:\\/])?(?<act>[a-zA-Z0-9_]+)?$/";

    public static function installExtend(\Latte\Engine $latte) {
        $me = new static($latte->getCompiler());
        $latte->addFilter("asset", array($me, "filterAsset"));
        $latte->addFilter("todate", array($me, "todate"));
        $latte->addFilter("totime", array($me, "totime"));
        $latte->addFilter("default", array($me, "filterDefault"));
        $me->addMacro("asset", array($me, "macroAsset"), array($me, "macroEndAsset"));
        $me->addMacro("path", array($me, "macroPath"), array($me, "macroEndPath"));
        $me->addMacro("assetWrite", array($me, "macroAssetWrite"));
        $me->addMacro("import", array($me, 'macroImport'));
        $me->addMacro("literal", function (MacroNode $node, PhpWriter $writer) {
            return $writer->write('');
        }, function (MacroNode $node, PhpWriter $writer) {
            return $writer->write('');
        });
        $me->addMacro("control", array($me, 'macroControl'));
    }

    /**
     * n:ifcontent
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     * @throws CompileException
     */
    public function macroAsset(MacroNode $node, PhpWriter $writer) {
        return $writer->write('ob_start()');
    }

    /**
     * n:ifcontent
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroEndAsset(MacroNode $node, PhpWriter $writer) {
        return $writer->write('echo phpex\Asset\Assetic::parseHtml(ob_get_clean(),"%node.args")');
    }

    public function macroPath(MacroNode $node, PhpWriter $writer) {
        return $writer->write('ob_start();');
    }

    public function macroEndPath(MacroNode $node, PhpWriter $writer) {
        return $writer->write('echo phpex\Asset\Assetic::path(ob_get_clean(),%node.args)');
    }

    public function macroAssetWrite(MacroNode $node, PhpWriter $writer) {
        return $writer->write('echo phpex\Assetic\Assetic::render("",%node.args)');
    }

    /**
     * @param MacroNode $node
     * @param PhpWriter $writer
     * @return string
     */
    public function macroImport(MacroNode $node, PhpWriter $writer) {
        $pattern = $this->pattern;
        $filePath = trim(trim($node->args, "\""), "'");
        $configs = C("view");
        $basename = basename($filePath);
        $suffix = strstr($basename, ".");
        if ($suffix)
            $filePath = substr($filePath, 0, -strlen($suffix));
        else {
            $suffix = "." . getkey($configs["RelateEngine"], $configs["engine"]);
        }
        if (!$filePath) { // 空值
            return "";
        } elseif (preg_match($pattern, $filePath, $matches)) { // index
            /* @var $app AppInterface */
            if (!isset($matches["app"]) || empty($matches["app"])) {
                $appName = ucfirst(R()->getAppName());
            } else {
                $appName = ucfirst(substr($matches["app"], 0, -1));
            }

            $app = ins()->get("app." . $appName);
            if (!isset($matches["theme"]) || empty($matches["theme"])) {
                $theme = $app->getTheme() ? $app->getTheme() . "/" : "";
            } else {
                $theme = substr($matches["theme"], 0, -1) . "/";
            }
            if (!isset($matches["con"]) || empty($matches["con"])) {
                $con = R()->getController();
            } else {
                $con = substr($matches["con"], 0, -1);
            }
            $con = ucfirst($con);
            if (!isset($matches["act"]) || empty($matches["act"])) {
                $act = R()->getAction();
            } else {
                $act = $matches["act"];
            }
            $filePath = $app->getRoot() . "/View/" . $theme . $con . "/" . $act;
        }
        $filePath .= $suffix;
        $filePath = str_replace("\\", "/", $filePath);
        if (!is_file($filePath)) {
            $code = "echo  '<!--" . $filePath . "-->'";
            return ($node->modifiers) ? $writer->write('ob_start(); %raw; echo %modify(ob_get_clean())', $code) : $code;
        }
        $code = $writer->write('$_l->templates[%var]->getEngine()->render("' . $filePath . '", $template->getParameters()+ get_defined_vars())', $this->getCompiler()->getTemplateId());
        if ($node->modifiers) {
            return $writer->write('ob_start(); %raw; echo %modify(ob_get_clean())', $code);
        } else {
            return $code;
        }
    }

    public function macroControl(MacroNode $node, PhpWriter $writer) {
        return $writer->write('echo \Engine\phpexEngine::controlWriter("' . $node->args . '")');
    }

    public static function controlWriter($args) {
        $args = explode(" ", $args);

        list($app, $control, $action) = explode(":", $args[0]);
        $app = ins()->get("app." . ucfirst($app));
        $parameter = array();
        if (isset($args[1]))
            parse_str($args[1], $parameter);
        /* @var $response \phpex\Foundation\Response */
        $response = $app->run(ucfirst($control), $action, $parameter);
        if ($response instanceof \phpex\Foundation\Response) {
            if ($response->getStatusCode() == 200) {
                return $response->getContent();
            } else {
                throw new \HttpInvalidParamException("response StatusCode must be 200");
            }
        } elseif (is_scalar($response)) {
            return (string) $response;
        } else {
            throw new \HttpInvalidParamException("response types must be string or Response object");
        }
    }

    public function todate($date, $format = "Y-m-d") {
        return todate($date, $format);
    }

    public function totime($date, $format = "Y-m-d H:i:s") {
        return $this->todate($date, $format);
    }
    public function filterDefault($string = "", $default = "") {
        if (trim(strval($string)) === "" || $string === null) {
            return $default;
        }
        return $string;
    }
    //123123

}