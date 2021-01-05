<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Util\ORG;

use phpex\Foundation\Response;

/**
 * Description of Images
 *
 * @author Administrator
 */
class Image {

    static private $error;

    /**
     *
     * @param type $string
     * @param type $width
     * @param type $height
     * @param type $image_code
     * @return Response Description
     */
    static public function buildString($string = '', $width = 48, $height = 22, $image_code = "") {
        $length = strlen($string);
        $width = ($length * 9 + 10) > $width ? $length * 9 + 10 : $width;
        $verify = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($verify, 0xFF, 0xFF, 0xFF);
        imagefill($verify, 0, 0, $white); //置换背景颜色
        //将随机串写入画布
        $blue = imagecolorallocate($verify, 43, 51, 204);
        $red = imagecolorallocate($verify, 208, 44, 44);
        $green = imagecolorallocate($verify, 51, 255, 204);
        //布置干扰线
        imagedashedline($verify, rand(0, $width), 0, rand(0, $width), $height, $red);
        imagedashedline($verify, rand(0, $width), 0, rand(0, $width), $height, $blue);
        imagedashedline($verify, rand(0, $width), 0, rand(0, $width), $height, $blue);
        imageline($verify, 0, rand(0, $height), $width, rand(0, $height), $green);
        //添加文字，英文
        imagestring($verify, 5, rand(2, ($width / $length)), rand(1, $height / 3), $string, $blue);
        imagedashedline($verify, rand(0, $width), 0, rand(0, $width), $height, $blue);
        imagedashedline($verify, rand(0, $width), 0, rand(0, $width), $height, $blue);
        ob_start();
        imagejpeg($verify);
        return new Response(ob_get_clean(), 200, array('Content-Type' => "image/jpeg", "Image-Code" => $image_code));
    }

    static public function buildStringBase($string = '', $width = 48, $height = 22, $image_code = ""){
        $length = strlen($string);
        $width = ($length * 9 + 10) > $width ? $length * 9 + 10 : $width;
        $verify = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($verify, 0xFF, 0xFF, 0xFF);
        imagefill($verify, 0, 0, $white); //置换背景颜色
        //将随机串写入画布
        $blue = imagecolorallocate($verify, 43, 51, 204);
        $red = imagecolorallocate($verify, 208, 44, 44);
        $green = imagecolorallocate($verify, 51, 255, 204);
        //布置干扰线
        imagedashedline($verify, rand(0, $width), 0, rand(0, $width), $height, $red);
        imagedashedline($verify, rand(0, $width), 0, rand(0, $width), $height, $blue);
        imagedashedline($verify, rand(0, $width), 0, rand(0, $width), $height, $blue);
        imageline($verify, 0, rand(0, $height), $width, rand(0, $height), $green);
        //添加文字，英文
        imagestring($verify, 5, rand(2, ($width / $length)), rand(1, $height / 3), $string, $blue);
        imagedashedline($verify, rand(0, $width), 0, rand(0, $width), $height, $blue);
        imagedashedline($verify, rand(0, $width), 0, rand(0, $width), $height, $blue);
        ob_start();
        imagejpeg($verify);
        return new Response("data:image/jpeg;base64,".base64_encode(ob_get_clean()), 200, array('Content-Type' => "image/jpeg", "Image-Code" => $image_code));
    }

    /**
     * 缩略图生成
     * @param string $source 原图路径
     * @param int $weight 缩略图的宽度
     * @param int $height 缩略图的高度
     * @param string $des 缩略图的保存路径
     */
    static public function thumb($source, $saveName, $width = null, $height = null) {
        //获取原图信息
        if (is_file($source)) {
            $info = self::getImageInfo($source);
            if (false === $info) {
                self::$error = 'Failed to get image information';
                return false;
            }
        } else {
            self::$error = 'Image not exists ';
            return false;
        }

        if (is_null($width) && is_null($height)) {
            self::$error = 'You need to specify the width and height of at least one';
            return false;
        } elseif (is_null($width)) {
            $width = ($info['width'] * $height) / $info['height'];
        } elseif (is_null($height)) {
            $height = ($info['height'] * $width) / $info['width'];
        }
        $newImage = imagecreatetruecolor($width, $height);

        $functionName = "imagecreatefrom" . $info['type'];
        $sourImage = $functionName($source);

        imagecopyresampled($newImage, $sourImage, 0, 0, 0, 0, $width, $height, $info['width'], $info['height']);
        if (false === mk_dir(dirname($saveName))) {
            self::$error = ' Directory cannot write : ' . $savePath;
        }
        imagejpeg($newImage, $saveName);
    }

    /**
     * 对图片打水印
     * 如果水印图片的宽高之一大于原图片，则返回false
     * 如果没有指定处理后图片保存位置，则会覆盖原图片
     * 如果没有指定水印的透明度，则默认为80
     * 如果没有指定水印位置，则默认为随机位置
     * @param string $resource 被打水印图片地址
     * @param string $water 水印图片地址
     * @param string $savename 处理后图片保存地址
     * @param int $alpha 水印图片透明度
     * @param int $position 水印位置【0->中间位置, 1->左上角, 2->右上角, 3->左下角, 4->右下角, 5->随机位置】
     */
    static public function water($resource, $water, $savename = null, $alpha = 80, $position = 5, $margin = 0) {
        //判断源文件和水印文件是否存在，不存在则返回false
        if (!file_exists($resource) || !file_exists($water)) {
            self::$error = '缺少源文件或者水印文件';
            return false;
        }
        if (!is_numeric($alpha) || $alpha > 100) {
            $alpha = 80;
        }
        //获取文件信息
        $resourceInfo = self::getImageInfo($resource);
        $waterInfo = self::getImageInfo($water);
        //如果获取文件信息失败，则返回false
        if (false === $resourceInfo || false === $waterInfo) {
            self::$error = '获取文件信息失败';
            return false;
        }
        //如果水印图片的宽高之一大于原图片，则返回false
        if ($resourceInfo['width'] < $waterInfo['width'] || $resourceInfo['height'] < $waterInfo['height']) {
            self::$error = '源文件的宽高小于水印图片宽高';
            return false;
        }
        //计算水印的位置
        $positionArr = self::getPosition($resourceInfo, $waterInfo, $position, $margin);
        //创建图像资源
        $sCreateFun = "imagecreatefrom" . $resourceInfo['type'];
        $sImage = $sCreateFun($resource);
        $wCreateFun = "imagecreatefrom" . $waterInfo['type'];
        $wImage = $wCreateFun($water);

        ////设定图像的混色模式
        imagealphablending($wImage, true);

        //生成混合图像
        imagecopymerge($sImage, $wImage, $positionArr['left'], $positionArr['top'], 0, 0, $waterInfo['width'], $waterInfo['height'], $alpha);

        //输出图像
        $ImageFun = 'Image' . $resourceInfo['type'];
        mk_dir(dirname($savename));
        //如果没有给出保存文件名，默认为原图像名
        if (is_null($savename)) {
            $savename = $resource;
            @unlink($resource);
        }
        //保存图像
        $ImageFun($sImage, $savename);
        imagedestroy($sImage);
    }

    static private function getPosition($resourceInfo, $waterInfo, $position, $margin = 0) {
        switch ($position) {
            case 0: //随机
                $left = $resourceInfo['width'] - $waterInfo['width'];
                $top = $resourceInfo['height'] - $waterInfo['height'];
                return array('left' => mt_rand(0 + $margin, $left - $margin), 'top' => mt_rand(0 + $margin, $top - $margin));
            case 1: //左上
                return array('left' => 0 + $margin, 'top' => 0 + $margin);
            case 2: //中上
                $left = ($resourceInfo['width'] - $waterInfo['width']) / 2;
                return array('left' => $left, 'top' => 0 + $margin);
            case 3: //右上
                $left = $resourceInfo['width'] - $waterInfo['width'];
                return array('left' => $left - $margin, 'top' => 0 + $margin);
            case 4: //中左
                $top = ($resourceInfo['height'] - $waterInfo['height']) / 2;
                return array('left' => 0 + $margin, 'top' => $top);
            case 5: //中中
                $left = ($resourceInfo['width'] - $waterInfo['width']) / 2;
                $top = ($resourceInfo['height'] - $waterInfo['height']) / 2;
                return array('left' => $left, 'top' => $top);
            case 6: //中右
                $left = $resourceInfo['width'] - $waterInfo['width'];
                $top = ($resourceInfo['height'] - $waterInfo['height']) / 2;
                return array('left' => $left - $margin, 'top' => $top);
            case 7://下左
                $top = $resourceInfo['height'] - $waterInfo['height'];
                return array('left' => 0 + $margin, 'top' => $top - $margin);
            case 8://下中
                $top = $resourceInfo['height'] - $waterInfo['height'];
                $left = ($resourceInfo['width'] - $waterInfo['width']) / 2;
                return array('left' => $left, 'top' => $top - $margin);
            case 9://下右
                $left = $resourceInfo['width'] - $waterInfo['width'];
                $top = $resourceInfo['height'] - $waterInfo['height'];
                return array('left' => $left, 'top' => $top - $margin);
        }
    }

    /**
     * 取得图像信息
     * @static
     * @access public
     * @param string $image 图像文件名
     * @return mixed
     * come from ThinkPHP
     */
    static function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageSize = filesize($img);
            $info = array(
                "width" => $imageInfo[0],
                "height" => $imageInfo[1],
                "type" => $imageType,
                "size" => $imageSize,
                "mime" => $imageInfo['mime']
            );
            return $info;
        } else {
            return false;
        }
    }

    /**
     *
     * @param type $resource
     * @param type $savename
     * @param type $newWidth
     * @param type $newHeight
     * @param type $x
     * @param type $y
     * @return boolean
     */
    public static function clip($resource, $savename, $newWidth, $newHeight, $x = 0, $y = 0) {
        if ($newWidth < 1 || $newHeight < 1) {
            self::$error = "params width or height error !";
            return false;
        }
        if (!file_exists($resource)) {
            self::$error = $resource . " is not exists !";
            return false;
        }
        $clipInfo = self::getImageInfo($resource);
        $support_type = array('jpeg', 'png', 'gif');
        if (!in_array($clipInfo['type'], $support_type, true)) {
            self::$error = "this type of image does not support! only support jpg , gif or png";
            return false;
        }
        $wCreateFun = "imagecreatefrom" . $clipInfo['type'];
        $src_img = $wCreateFun($resource);
        $w = imagesx($src_img);
        $h = imagesy($src_img);
        $ratio_w = 1.0 * $newWidth / $w;
        $ratio_h = 1.0 * $newHeight / $h;
        $ratio = 1.0;
        $imagesFun = "image" . $clipInfo['type'];
        mk_dir(dirname($savename));
        if (($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) {
            if ($ratio_w < $ratio_h) {
                $ratio = $ratio_h;
            } else {
                $ratio = $ratio_w;
            }
            $inter_w = (int)($newWidth / $ratio);
            $inter_h = (int)($newHeight / $ratio);
            $inter_img = imagecreatetruecolor($inter_w, $inter_h);
            imagecopy($inter_img, $src_img, 0, 0, 0, 0, $inter_w, $inter_h);
            $new_img = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($new_img, $inter_img, 0, 0, 0, 0, $newWidth, $newHeight, $inter_w, $inter_h);
            $imagesFun($new_img, $savename, 100);
        } else {
            $ratio = $ratio_h > $ratio_w ? $ratio_h : $ratio_w;
            $inter_w = (int)($w * $ratio);
            $inter_h = (int)($h * $ratio);
            $inter_img = imagecreatetruecolor($inter_w, $inter_h);
            imagecopyresampled($inter_img, $src_img, 0, 0, (int)$x, (int)$y, $inter_w, $inter_h, $w, $h);
            $new_img = imagecreatetruecolor($newWidth, $newHeight);
            imagecopy($new_img, $inter_img, 0, 0, 0, 0, $newWidth, $newHeight);
            $imagesFun($new_img, $savename, 100);
        }
    }

    /**
     * 返回错误信息
     * @return string self::$error;
     */
    static public function getError() {
        return self::$error;
    }

}
