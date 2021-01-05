<?php

use phpex\Library\Instance;
use phpex\Library\MainInterface;
use Monolog\Logger;
use phpex\Foundation\Request;

/**
 * @return phpex\Foundation\Request
 */
function Q() {
    if (!ins()->has("core.request"))
        ins()->addInstance("core.request", Request::createFromGlobals());
    return ins()->get("core.request");
}

/**
 * 实例管理器
 * @return phpex\Library\Instance
 */
function ins() {
    return Instance::getInstance();
}

/**
 * 路由管理器
 * @return \phpex\Library\Route
 */
function R() {
    if (!ins()->has("core.route")) {
        ins()->addInstance("core.route", phpex\Library\Route::getInstance());
    }
    return ins()->get("core.route");
}

function forward() {

}

/**
 * 根据路由获取url
 * @param string $name 路由名称
 * @param array|string $parameters 路由参数
 * @param boolean $intact 是否返回完整路径,默认 false
 * @return string
 */
function url($name, $parameters = array(), $intact = false) {
    if (is_array($parameters)) {
        $params = &$parameters;
    } else {
        parse_str($parameters, $params);
    }
    if ($name{0} == "~") {
        $name = substr($name, 1);
        $intact = true;
    }
    return R()->getUrlForRoute($name, $params, $intact);
}

/**
 * 解析并编译资源
 * @param string $output
 * @param string $handler
 */
function path($output) {
    if (!ins()->has("core.asset")) {
        ins()->addInstance("core.asset", new phpex\Asset\Asset());
    }
    return ins()->get("core.asset")->path($output);
}

/**
 * @param array|string $name 配置名
 * @param mixed $value 配置值
 * @return ConfigureInterface|mixed
 */
function C() {
    $args = func_get_args();
    if (count($args) == 0)
        return ins()->get("core.config");
    if (count($args) == 1 && is_scalar($args[0]))
        return ins()->get("core.config")->get($args[0]);
    if (count($args) == 2 && is_scalar($args[0]))
        return ins()->get("core.config")->set($args[0], $args[1]);
    if (empty($args) && is_array($args[0]))
        return ins()->get("core.config")->sets($args[0]);
}

/**
 * 将文件内容解析成数组
 * @param string $file 文件的路径
 * @return array|null
 */
function parseFile($file) {
    if (!ins()->has("core.parse")) {
        ins()->newInstance("core.parse", 'phpex\\Util\\ORG\\Parse');
    }
    return ins()->get("core.parse")->parseFile($file);
}

/**
 * 将字符串当作指定的格式解析成数组
 * @param string $input 要解析的字符串
 * @param string $type 可以是yml，json，xml，php
 * @return array|null
 */
function parseString($input, $type = 'yml') {
    if (!ins()->has("core.parse")) {
        ins()->newInstance("core.parse", 'phpex\\Util\\ORG\\Parse');
    }
    return ins()->get("core.parse")->parseString($input, $type);
}

function arrDump(array $array, $type = "yml", $level = 2) {
    try {
        switch (strtolower($type)) {
            case "yml":
                $configs = \phpex\Util\Yaml\Yaml::dump($array, $level);
                return (string)$configs;
            case "json":
                $configs = json_encode($array);
                return (string)$configs;
            case "xml":
                $configs = phpex\Util\Xml\XmlParse::dump($array);
                return (string)$configs;
            case "php":
                $configs = var_export($array, true);
                return (string)$configs;
            default:
                return "";
        }
    } catch (\Exception $ex) {
        $debug = debug_backtrace();
        E(sprintf($ex->getMessage() . " for:'%s'", dirtrim($debug[0]["file"])));
    }
}

/**
 *
 * @return MainInterface
 */
function main() {
    return ins()->get("core.main");
}

function logger($type = Logger::ERROR, $errno = "INFO") {
    static $errortype = array(
        E_ERROR => "E_ERROR",
        E_PARSE => "E_PARSE",
        E_CORE_ERROR => "E_CORE_ERROR",
        E_COMPILE_ERROR => "E_COMPILE_ERROR",
        E_USER_ERROR => "E_USER_ERROR",
        E_STRICT => "E_STRICT",
        E_USER_WARNING => "E_USER_WARNING",
        E_USER_NOTICE => "E_USER_NOTICE",
    );
    $log = new Logger(isset($errortype[$errno]) ? $errortype[$errno] : $errno);
    $path = main()->getLogDir() . "/" . main()->getEnv() . date("_Ym_") . $errno . ".log";
    if (!is_dir(dirname($path)))
        mkdir(dirname($path), 0777, true);
    $log->pushHandler(new Monolog\Handler\StreamHandler($path, $type));
    return $log;
}

function loginfo($path, $message, $context = array()) {
    $log = new Logger("INFO");
    $path = main()->getLogDir() . "/" . main()->getEnv() . $path . ".log";
    if (!is_dir(dirname($path)))
        mkdir(dirname($path), 0777, true);
    $log->pushHandler(new Monolog\Handler\StreamHandler($path, "INFO"));
    $log->addInfo($message, $context);
}

function typeof($var) {
    if (gettype($var) == "object") {
        return get_class($var);
    }
    return gettype($var);
}

/**
 * 返回数组的key
 * @param array $array 输入要查找的数组
 * @param mixed $value 需要查找的值
 * @return type
 */
function getkey($array) {
    $args = func_get_args();
    $keys = count($args) > 1 ? array_keys($array, $args[1]) : array_keys($array);
    if (count($keys) == 1) {
        return current($keys);
    } elseif (count($keys) == 0) {
        return null;
    }
    return $keys;
}

/**
 * 获取DModel实例
 * @return Hint\DModelHint
 */
function D() {
    if (!ins()->has("core.DModel")) {
        ins()->newInstance("core.DModel", 'phpex\\DModel\\DModelInstance');
    }
    return ins()->get("core.DModel");
}

/**
 *
 * @param type $message
 */
function E($message) {
    $args = func_get_args();
    array_shift($args);
    if (count($args) > 0)
        throw new phpex\Error\Exception(vsprintf($message, $args));
    else
        throw new phpex\Error\Exception($message);
}

/**
 * 抛出异常
 * @param string $message 异常信息
 * @param string $flie 异常所在文件
 * @param integer $line 异常行号
 * @param  string $srcCode 源代码
 * @param mixed $_param 格式化时的替代参数【可选】
 * @param mixed $_param 格式化时的替代参数【可选】
 * @throws phpex\Error\Exception
 */
function throws($message, $flie, $line, $srcCode) {
    $args = func_get_args();
    $args = array_slice($args, 4);
    $e = new phpex\Error\Exception(vsprintf($message, $args));
    throw $e->setSource($srcCode, $line, $flie);
}

function debug_throws($message) {
    $args = func_get_args();
    array_shift($args);
    $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $e = new phpex\Error\Exception(vsprintf($message, $args));
    throw $e->setSource(0, $debug[1]["line"], $debug[1]["file"]);
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo = true, $label = null, $strict = true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    $debug = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $from = "<b>" . dirtrim($debug[0]["file"]) . ";line:" . $debug[0]["line"] . "</b>\n";
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $from . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $from . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }

    if ($echo) {
        if (!headers_sent()) {
            header("Content-Type:text/html; charset=utf-8");
            header("Content-Method:dump");
        }
        echo($output);
        return null;
    } else
        return $output;
}

/**
 * @param string $name
 * @param array $config
 * @return phpex\Driver\Doctrine
 */
function DM($name = "default", $config = array()) {
    static $configs = array();
    static $doctrines = array();
    if (isset($doctrines[$name])) {
        return $doctrines[$name];
    }
    if (!isset($configs[$name])) {
        $database = C("database");
        $configs[$name] = isset($database[$name]) ? array_merge($database[$name], $config) : $config;
    }
    $doctrines[$name] = new \phpex\Driver\Doctrine();
    $doctrines[$name]->create($configs[$name], $configs[$name]["paths"], $configs[$name]["mapType"], main()->getEnv() == "dev" ? false : true);
    return $doctrines[$name];
}

function T($file, $line) {
    static $lasttime = 0;
    static $laststr = "\n";
    $microtime = microtime(true);
    $laststr .= sprintf("[%s] [%s:%s] [%f] [%f]\n", now(), $file, $line, $microtime, $lasttime == 0 ? 0 : $microtime - $lasttime);
    $lasttime = $microtime;
    return $laststr;
}

/**
 * @return \phpex\Util\ORG\Seo
 */
function seo() {
    if (!ins()->has("org.Seo")) {
        ins()->addInstance("org.Seo", new \phpex\Util\ORG\Seo());
    }
    return ins()->get("org.Seo");
}

/**
 * 剪切文件路径
 * @param string $dir 原始路径
 * @param integer $show 显示的路径级数
 * @param string $replace 被剪切后用于替代的字符
 * @return type
 */
function dirtrim($dir, $show = 3, $replace = ".../") {
    $showname = "";
    for ($i = 0; $i < $show; $i++) {
        $showname = $showname ? basename($dir) . "/" . $showname : basename($dir);
        $dir = dirname($dir);
    }
    return $dir ? $replace . $showname : $showname;
}

function sysmd5($str) {
    return md5($str);
}

function encrypt($str) {

    $crc32 = hash('crc32', $str);
    $crc32b = hash('crc32b', $str);
    $sha1 = hash('sha1', $str);

    return md5($crc32 . $sha1 . $crc32b);
}

function now($timestamp = null, $format = "Y-m-d H:i:s") {
    return date($format, $timestamp ?: time());
}

function nowTime($timestamp = null, $format = "Y-m-d H:i:s") {
    return DateTime::createFromFormat($format, now($timestamp, $format));
}

/**
 * 时间比较，如果A>B 返回1 ,A<=B,返回0
 * @param DateTime $timeA
 * @param DateTime $timeB
 * @return int
 */

function diffTime(DateTime $timeA, DateTime $timeB) {
    $diff = $timeA->diff($timeB);
    return $diff->invert;
}


function addTime($addStr, DateTime $time = null, $new = true) {
    if ($new) {
        $newTime = $time ? clone $time : nowTime();
        $newTime->add(\DateInterval::createFromDateString($addStr));
        return $newTime;
    }
    if (!$time)
        $time = nowTime();
    $time->add(\DateInterval::createFromDateString($addStr));
    return $time;
}

/**
 * @param DateTime $endTime
 * @param string $type
 * @param DateTime|null $startTime
 * @return string
 */
function countdownTime(DateTime $endTime, $type = "d", DateTime $startTime = null) {
    $startTime or $startTime = nowTime();
    $diff = $endTime->diff($startTime);
    if ($diff->invert == 0) {
        return "0 秒";
    }
    $return = "";
    switch ($type) {
        case "d":
            if ($diff->days)
                $return = $diff->days . "天";
            if ($diff->h) {
                $return .= $diff->h . "小时";
            }
            if ($diff->i && !$diff->days) {
                $return .= $diff->i . "分";
            }
            if ($diff->s && !$diff->days && !$diff->h) {
                $return .= $diff->s . "秒";
            }
            return $return;
        case "h":
            $h = ($diff->days * 24) + $diff->h;
            if ($h) {
                $return .= $h . "小时";
            }
            if ($diff->i && $h < 10) {
                $return .= $diff->i . "分";
            }
            if ($diff->s && !$h) {
                $return .= $diff->s . "秒";
            }
            return $return;
        default:
            if ($diff->y) {
                $return = $diff->y . "年";
            }
            if ($diff->m)
                $return = $diff->m . "月";
            if ($diff->d)
                $return = $diff->m . "日";
            if ($diff->h && !$diff->y && !$diff->m) {
                $return .= $diff->h . "小时";
            }
            if ($diff->i && !$diff->days) {
                $return .= $diff->i . "分";
            }
            if ($diff->s && !$diff->days && !$diff->h) {
                $return .= $diff->i . "秒";
            }
            return $return;
    }
}

function nowObj($timestamp = null, $format = "Y-m-d H:i:s") {
    return nowTime($timestamp, $format);
}

/**
 * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
function rand_string($len = 6, $type = '', $addChars = '') {
    $str = '';
    switch ($type) {
        case 0:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 1:
            $chars = str_repeat('0123456789', 3);
            break;
        case 2:
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
            break;
        case 3:
            $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
            break;
        case 4:
            $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借" . $addChars;
            break;
        default :
            // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
            $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
            break;
    }
    if ($len > 10) {//位数过长重复字符串一定次数
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    if ($type != 4) {
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
    } else {
        // 中文随机字
        for ($i = 0; $i < $len; $i++) {
            $str .= msubstr($chars, floor(mt_rand(0, mb_strlen($chars, 'utf-8') - 1)), 1);
        }
    }
    return $str;
}

function str2file($str, $filePath) {
    $fp = fopen($filePath, 'w+');
    fwrite($fp, $str);
    fclose($fp);
}

function toweight($num) {
    return 1 << ($num - 1);
}

function L($key) {
    return \phpex\Util\ORG\Lang::translation($key);
}

function msubstr($str, $start = 0, $length = 20, $suffix = "...", $charset = "utf-8") {
    if (function_exists("mb_substr")) {
        return mb_substr($str, $start, $length, $charset) . $suffix;
    } elseif (function_exists("iconv_substr")) {
        return iconv_substr($str, $start, $length, $charset) . $suffix;
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("", array_slice($match[0], $start, $length));
    return $slice . $suffix;
}

function mstrlen($str, $charset = "utf-8") {
    if (function_exists("mb_substr")) {
        return mb_strlen($str, $charset);
    } elseif (function_exists("iconv_substr")) {
        return iconv_strlen($str, $charset);
    }
    $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    return count($match[0]);
}

/**
 * 导出excel
 * @param type $fileName 文件名
 * @param type $header <pre>格式： 列名=>'字段名:表头%字段格式'
 *                            如：array(A=>"userName:用户名",B=>"money:金额%f")
 *                            %f(浮点型)
 *                            %s(字符)
 *                            %str(字符)
 *                            %n(整型)
 *                            %b(布尔型)
 *                            %null(NULL)
 *                            %inlineStr
 *                            %t（DataTime）
 *
 *                      </pre>
 * @param type $data 数据
 * @param bjo $return 数据
 */
function excelExprot($fileName, array $header, array $data, $return = true) {
    if (empty($data) || empty($header)) {
        E("数据和表头不能传空值！");
    }
    $formats = array();
    $fields = array();
    $PHPExcel = new \PHPExcel();
    $PHPExcel->setActiveSheetIndex(0);
    $sheet = $PHPExcel->getActiveSheet();
    foreach ($header as $column => $head) {
        if (!preg_match("/^[A-Z]{1,2}$/", $column) || !is_string($head)) {
            E("表头格式不正确:%s!", $column);
        }
        preg_match("/^(?<field>[a-zA-Z_0-9]+)\:(?<header>.+?)(%(?<format>s|f|str|n|b|null|inlineStr|t))?$/", $head, $match);
        if (!$match) {
            E("表头格式不正确:%s!", $column);
        }
        $sheet->getColumnDimension($column)->setAutoSize(true);
        $sheet->setCellValue($column . '1', $match['header']);
        $sheet->getStyle($column . '1')->getFont()->setBold(TRUE);
        $formats[$column] = isset($match['format']) ? $match['format'] : "";
        $fields[$column] = $match['field'];
    }
    $row = 2;
    foreach ($data as $item) { //行写入
        foreach ($fields as $column => $field) {// 列写入
            if ($item[$field] instanceof DateTime)
                $value = $item[$field]->getTimestamp() ? $item[$field]->format("Y-m-d H:i:s") : "0000-00-00 00:00:00";
            elseif (is_scalar($item[$field])) {
                $value = $item[$field];
            } else {
                $value = (string)$item[$field];
            }
            if ($formats[$column]) {
                $sheet->setCellValueExplicit($column . $row, $value, $formats[$column]);
            } else {
                $sheet->setCellValue($column . $row, $value);
            }
        }
        $row++;
    }
    $fileName = iconv("utf-8", "gb2312", $fileName);
    $writer = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5');
    if ($return) {
        $headers = array(
            "Content-Type" => "application/vnd.ms-excel",
            "Content-Disposition" => sprintf('attachment;filename="%s"', $fileName),
            "Cache-Control" => "max-age=0",
        );
        ob_start();
        $writer->save('php://output');
        return new phpex\Foundation\Response(ob_get_clean(), 200, $headers);
    } else {
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = false) {
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 添加普通日志
 * @param string $name 日志名称
 * @param string $message 日志内容
 * @param array $context 与日志相关的上下文
 * @param integer $userId 操作人id
 */
function addLog($name, $message, array $context = array(), $userId = 0) {
    $log = new \Monolog\Logger($name);
    $path = RUNTIME_PATH . "Logs/" . date("Ym") . "/" . $userId . ".log";
    mk_dir(dirname($path));
    $log->pushHandler(new \Monolog\Handler\StreamHandler($path, \Monolog\Logger::INFO));
    $log->addInfo($message, $context);
}

function mk_dir($dir, $mode = 0777) {
    if (is_dir($dir) || mkdir($dir, $mode, true))
        return true;
    if (!mk_dir(dirname($dir), $mode))
        return false;
    return @mkdir($dir, $mode);
}

/**
 * 隐藏信息
 * @param string $info 信息字符串
 * @param integer $type 字符串类型 0:自动识别 1:手机号码 2:邮箱号码 3:银行卡/信用卡 4:用户名/用户编码  5:身份证 6:中英混合
 */
function hideInfo($info, $cuts = "", $replace = "", $type = 0) {
    static $patterns = array(
        1 => array("/^1[3-9]\d{9}$/", "3,4", "****"),
        2 => array("/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/", "3,3,@", "***"),
        3 => array("/^\d{16}(\d{3})?$/", "0,4", "*** *** "),
        4 => array("/^[a-zA-Z][a-zA-Z_0-9]+$/", "3,3", "*"),
        5 => array("/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[A-Z])$/", "6,4", "****"),
        6 => array("/[\x7f-\xffa-zA-Z_0-9]+/", "0,2", "*")
    );
    if ($type == 0) {
        foreach ($patterns as $infotype => $pattern) {
            if (preg_match($pattern[0], $info)) {
                $type = $infotype;
                break;
            }
        }
    } elseif (!isset($patterns[$type]) || !preg_match($patterns[$type][0], $info)) {
        return $info;
    }
    $cuts = explode(",", $cuts ?: $patterns[$type][1]);
    if (isset($cuts[2])) {
        $retainstr = strstr($info, $cuts[2]);
        $info = substr($info, 0, -(strlen($retainstr)));
    } else {
        $retainstr = "";
    }
    $len = mstrlen($info);
    if ($cuts[0] >= $len) {
        $cuts[0] = $len - 1 - $cuts[1];
    }
    if ($cuts[1] >= $len) {
        $cuts[1] = $len - 1 - $cuts[0];
    }
    return msubstr($info, 0, $cuts[0], "") . ($replace ?: $patterns[$type][2]) . msubstr($info, -$cuts[1], $cuts[1], "") . $retainstr;
}

/**
 *
 * @param string $path 路径
 * @param string $type 路径类型 如 src,small,middle,big
 */
function upload($path, $type = "src") {
    if (!ins()->has("core.asset")) {
        ins()->addInstance("core.asset", new phpex\Asset\Asset());
    }
    return ins()->get("core.asset")->uploadPath($path, $type);
}

//后加，上传的问题目录(www/upload),相对而言没有upload安全
function uploads($path, $type = "src") {
    return Q()->getBasePath().'/upload'.$path;
}
