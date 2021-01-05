<?php
//Jeechange function

function curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
    $output = curl_exec($ch); //执行并获取HTML文档内容
    curl_close($ch);
    return $output;
}

function todate($date, $format = "Y-m-d") {
    if (is_object($date)) {
        if ($date instanceof \DateTime) {
            $find = array("Y", "m", "d", "H", "i", "s");
            $replace = array("0000", "00", "00", "00", "00", "00");
            return $date->getTimestamp() > 0 ? $date->format($format) :
                str_replace($find, $replace, $format);
        }
    }
    return (string)$date;
}

function totime($date, $format = "Y-m-d H:i:s") {
    return todate($date, $format);
}


function curl_post($url, $post = array(), $headers = array()) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //跳过证书验证
    curl_setopt($ch, CURLOPT_POST, 1);
    if ($headers) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    $output = curl_exec($ch); //执行并获取HTML文档内容
    curl_close($ch);
    return $output;
}

function coin($money, $decimal = 2, $retain = true) {
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

function coinMoney($money, $decimal = 2) {
    return round($money, $decimal);
}

function json_cn($data) {
    return version_compare(phpversion(), '5.4.0') > 0 ? json_encode($data, JSON_UNESCAPED_UNICODE) : json_encode($data);
}