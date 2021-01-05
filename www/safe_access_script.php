<?php

error_reporting(0);

function error($info, $ext = "jpg") {
    if (in_array(strtolower($ext), array("jpg", "jpeg", "bmp", "png", "gif"))) {
        header('Content-type:image/gif');
        echo readfile(dirname(__DIR__) . "/app/Jeesell/View/Public/images/nopic_mid.gif");
        exit;
    }
    header("Content-Type:text/html; charset=utf-8");
    echo $info;
    exit;
}

$mime = array(
    "jpg" => "image/jpeg",
    "jpeg" => "image/jpeg",
    "bmp" => "image/bmp",
    "png" => "image/png",
    "gif" => "image/gif",
    "doc" => "application/msword",
    "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "xls" => "application/vnd.ms-excel",
    "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "ppt" => "application/vnd.ms-powerpoint",
    "pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
    "rar" => "application/x-rar-compressed",
    "zip" => "application/zip",
    "txt" => "text/plain",
    "pdf" => "application/pdf",
);
list($filename, $level) = explode("&", $_SERVER["QUERY_STRING"]);
if(!$level){
    $level='0';
}
$extensions = join("|", array_keys($mime));
$pattern = "/^upload(\/[a-zA-Z0-9_]+){2,}\.(?<ext>{$extensions})$/";
if (!preg_match($pattern, $filename, $match)) {
    error("不合法的文件名");
}

$basename = basename($filename);
$dirname = dirname($filename);


if (preg_match("/^[a-zA-Z]+\_(?<w>\d+)\_(?<h>\d+)\_(?<f>[a-zA-Z0-9_]+)\.(?<ext>jpg|jpeg|bmp|png|gif)$/", $basename, $matchs)) {
    $filename = $matchs["f"] . "." . $matchs["ext"];
    $filepath = dirname(__DIR__) . "/" . $dirname . "/" . $filename;
    if (!is_file($filepath)) {
        error("文件不存在", $matchs["ext"]);
    }
    $imageInfo = getimagesize($filepath, $info);
    if (!$imageInfo) {
        error("获取图像信息出错", $matchs["ext"]);
    }
    $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
    if (!$imageType) {
        error("获取图像信息出错", $matchs["ext"]);
    }
    $rh = ($matchs["w"] / $imageInfo[0]) * $imageInfo[1];
    $newImage = imagecreatetruecolor($matchs["w"], (int) $rh);
    $functionName = "imagecreatefrom" . $imageType;
    $sourImage = $functionName($filepath);
    imagecopyresampled($newImage, $sourImage, 0, 0, 0, 0, $matchs["w"], (int) $rh, $imageInfo[0], $imageInfo[1]);
    header('Cache-Control: Public');
    header('Content-type:image/jpeg');
    imagejpeg($newImage, null, 20);
    exit;
}


$filepath = dirname(__DIR__) . "/" . $filename;
$handle = fopen($filepath, "rb");

if (!$handle) {
    error("读取文件失败", $match["ext"]);
}
$ext = strtolower($match['ext']); //文件扩展
set_time_limit(0);

if (isset($mime[$ext]) && !empty($mime[$ext])) { //非下载头    
    if (in_array(strtolower($ext), explode("|", "jpg|jpeg|png|gif")) && $level !== "0") {
        $imageInfo = getimagesize($filepath, $info);
        if (!$imageInfo) {
            error("获取图像信息出错", $matchs["ext"]);
        }
        $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
        if (!$imageType) {
            error("获取图像信息出错", $matchs["ext"]);
        }
        $fstat = fstat($handle);
        if ($fstat['size'] < 81920 && !is_numeric($level)) {
            header('Cache-Control: Public');
            header('Content-type: ' . $mime[$ext]);
            header('Content-Length:' . $fstat['size']);
            fpassthru($handle);
            exit;
        }
        $functionName = "imagecreatefrom" . $imageType;
        $sourImage = $functionName($filepath);
        header('Cache-Control: Public');
        header('Content-type:image/jpeg');
        ob_start();
        if (!is_numeric($level)) {
            $level = 50;
        }
        if ((int) $level > 75) {
            $level = 75;
        } elseif ((int) $level < 15) {
            $level = 15;
        } else {
            $level = (int) $level;
        }
        imagejpeg($sourImage, null, $level);
        header('Content-Length:' . ob_get_length());
        imagedestroy($sourImage);
        ob_flush();
        exit;
    } else {
        header('Cache-Control: Public');
        header('Content-type: ' . $mime[$ext]);
    }
} else {//下载头
    $filename = basename($filepath);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Transfer-Encoding: binary');
}
flush(); // 刷新内容
while (!feof($handle)) {
    echo fread($handle, 1024); // 发送当前部分文件给浏览者
    ob_flush();
    flush(); // flush 内容输出到浏览器端             
}
fclose($handle);

