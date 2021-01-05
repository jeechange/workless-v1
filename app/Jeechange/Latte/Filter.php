<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Jeechange\Latte;

use Latte\Macros\MacroSet;
use Engine\ILatte;
use Latte\PhpWriter;
use Latte\MacroNode;

/**
 * Description of Filter
 *
 * @author Administrator
 */
class Filter extends MacroSet implements ILatte {

    static $navList = array(), $topNav = array();

    public static function installExtend(\Latte\Engine $latte) {
        $me = new static($latte->getCompiler());
        $latte->addFilter("split", array($me, "filterSplit"));
        $latte->addFilter("default", array($me, "filterDefault"));
        $latte->addFilter("coin", array($me, "coin"));
        $latte->addFilter("icoin", array($me, "icoin"));
        $latte->addFilter("msubstr", array($me, "msubstr"));
        $latte->addFilter("f_over_time", array($me, "f_over_time"));
        $latte->addFilter("todate", array($me, "todate"));
        $latte->addFilter("totime", array($me, "totime"));
        $me->addMacro("%", array($me, 'macroLang'));
    }

    public function macroLang(MacroNode $node, PhpWriter $writer) {
        if ($node->closing) {
            return $writer->write('echo %modify(L(ob_get_clean()))');
        } elseif ($node->isEmpty = ($node->args !== '')) {
            return $writer->write('echo %modify(L(%node.args))');
        } else {
            return 'ob_start()';
        }
    }

    public function macroControl(MacroNode $node, PhpWriter $writer) {
        return $writer->write('echo Jeechange\Latte\Filter::controlWriter("' . $node->args . '")');
    }

    public function macroShowsubmenu(MacroNode $node, PhpWriter $writer) {
        return $writer->write('echo Jeechange\Latte\Filter::showsubmenuWriter("' . $node->args . '")');
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
                E("response StatusCode must be 200");
            }
        } elseif (is_scalar($response)) {
            return (string) $response;
        } else {
            E("response types must be string or Response object");
        }
    }

    public function filterDefault($string = "", $default = "") {
        if (trim(strval($string)) === "" || $string === null) {
            return $default;
        }
        return $string;
    }

    public function todate($date, $format = "Y-m-d") {
        if (is_object($date)) {
            if ($date instanceof \DateTime) {
                $find = array("Y", "m", "d", "H", "i", "s");
                $replace = array("0000", "00", "00", "00", "00", "00");
                return $date->getTimestamp() > 0 ? $date->format($format) :
                        str_replace($find, $replace, $format);
            }
        }
        return (string) $date;
    }

    public function totime($date, $format = "Y-m-d H:i:s") {
        return $this->todate($date, $format);
    }

    /*
     * 根据时间戳判断当前时间为
     * 刚刚、1分钟前、1小时前、一天前
     */

    function f_over_time($time) {
        //获取今天凌晨的时间戳
        $day = strtotime(date('Y-m-d', time()));
        //获取昨天凌晨的时间戳
        $pday = strtotime(date('Y-m-d', strtotime('-1 day')));
        //获取现在的时间戳
        $nowtime = time();
        $time = strtotime($time);
        $tc = $nowtime - $time;
        if ($time < $pday) {
            $str = date('Y年m月d日', $time);
        } elseif ($time < $day && $time > $pday) {
            $str = "昨天";
        } elseif ($tc > 60 * 60) {
            $str = floor($tc / (60 * 60)) . "小时前";
        } elseif ($tc > 60) {
            $str = floor($tc / 60) . "分钟前";
        } else {
            $str = "刚刚";
        }
        return $str;
    }

    public function coin($money, $decimal = 2, $retain = true) {
        if ($money >= 1000000 && $money < 100000000) {
            if ($retain)
                return round($money / 10000, 2) . "万";
            $money = round($money / 10000, 2);
            list($int, $float) = explode(",", $money);
            $float = trim($float, "0");
            return ($float === "") ? $int . "万" : $int . "." . $float . "万";
        } elseif ($money >= 100000000) {
            if ($retain)
                return round($money / 100000000, 2) . "亿";
            $money = round($money / 100000000, 2);
            list($int, $float) = explode(".", $money);
            $float = trim($float, "0");
            return ($float === "") ? $int . "亿" : $int . "." . $float . "亿";
        } else {
            if ($retain)
                return number_format($money, $decimal);
            $money = number_format($money, $decimal);
            list($int, $float) = explode(".", $money);
            $float = trim($float, "0");
            return ($float === "") ? $int : $int . "." . $float;
        }
    }

    public function icoin($money, $decimal = 2) {
        return $this->coin($money, $decimal, false);
    }

    public function filterSplit($string, $key = 0) {
        $array = explode(',', $string);
        return $array[$key];
    }

    /**
     * 截取字符串
     * @param type $str
     * @param type $length
     * @param type $start
     * @param type $charset
     * @param type $suffix
     * @return type
     */
    function msubstr($str, $start = 0, $length = 20, $charset = "utf-8", $suffix = true) {
        if (strlen($str) / 3 > $length) {
            if (function_exists("mb_substr")) {
                return mb_substr($str, $start, $length, $charset) . '…';
            } elseif (function_exists("iconv_substr")) {
                return iconv_substr($str, $start, $length, $charset) . '…';
            }
            $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
            $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
            $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
            $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
            preg_match_all($re[$charset], $str, $match);
            $slice = join("", array_slice($match[0], $start, $length));
            if ($suffix) {
                return $slice;
            } else {
                return $slice;
            }
        }
        return $str;
    }

}
