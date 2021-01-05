<?php
/**
 * Created by PhpStorm.
 * User: 28613
 * Date: 2016/3/30
 * Time: 14:10
 */

namespace phpex\Asset;


use DOMDocument;
use phpex\Foundation\Request;
use phpex\Foundation\Response;
use phpex\Library\Main;


class Assetic {

    private static $lists = array();
    private static $css, $js, $name, $file;

    public static function add($body, $arg) {
        if ($arg == 1) {
            return self::parseHtml($body);
        }
        $args = explode(":", $arg);
        $name = $args[0];
        $render = isset($args[1]) && ($args[1] == 1);
        if (isset(self::$lists[$name])) {
            self::$lists[$name] .= $body;
        } else {
            self::$lists[$name] = $body;
        }
        if ($render) {
            $body = self::$lists[$name];
            unset(self::$lists[$name]);
            return self::parseHtml($body);
        }
        return "";
    }


    public static function render($name = "", $type = "") {
        $type = strtolower($type);
        if ($type && in_array($type, array("css", "js"))) {
            $type = "";
        }
        if ($name) {
            if (isset(self::$lists[$name])) {
                return "";
            }
            if ($type == "css" && isset(self::$css[$name])) {
                return "";
            }
            if ($type == "js" && isset(self::$js[$name])) {
                return "";
            }
            $htmlString = self::$lists[$name];
            if (!$type) {
                unset(self::$lists[$name]);
                self::$css[$name] = true;
                self::$js[$name] = true;
            }
            if ($type == "css") {
                self::$css[$name] = true;
            }
            if ($type == "js") {
                self::$js[$name] = true;
            }
            if (!$htmlString) {
                return "";
            }
            return self::parseHtml($htmlString, $type);
        }
        $return = "";

        foreach (self::$lists as $name => $htmlString) {
            if ($type == "css" && isset(self::$css[$name])) {
                continue;
            }
            if ($type == "js" && isset(self::$js[$name])) {
                continue;
            }
            if ($type == "css") {
                self::$css[$name] = true;
            } elseif ($type == "js") {
                self::$js[$name] = true;
            } else {
                self::$css[$name] = true;
                self::$js[$name] = true;
            }
            if (!$htmlString) {
                continue;
            }
            $return .= self::parseHtml($htmlString, $type);
        }
        return $return;
    }

    public static function parseHtml($htmlString, $urlpath = "") {
        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);
        $write = "";
        $links = array();
        $runName = router()->getName() . "-" . Main::self()->getRunApp()->getTheme();
        $basepath = main()->getRunApp()->getRoot() . "/View/" . Main::self()->getRunApp()->getTheme() . "/";
        $linkItems = $doc->getElementsByTagName('link');
        for ($i = 0; $i < $linkItems->length; $i++) {
            $href = $linkItems->item($i)->attributes->getNamedItem("href");
            if ($href) {
                $type = substr(strrchr($href->nodeValue, "."), 1);
                if (substr($href->nodeValue, 0, 10) === "../Public/") {
                    $file = substr($href->nodeValue, 3);
                    $links[] = array(
                        'type' => $type,
                        'file' => $basepath . substr($href->nodeValue, 3),
                        'url' => Main::self()->getAssetPath(array('path' => $runName, 'file' => substr($href->nodeValue, 10)))
                    );
                } elseif (substr($href->nodeValue, 0, 5) === "/app/") {
                    $keys = explode("/", substr($href->nodeValue, 5));
                    $keyName = $keys[0] . "-" . $keys[2];
                    $file = substr($href->nodeValue, strlen($keyName) + 18);
                    $links[] = array(
                        'type' => $type,
                        'file' => _APP_ROOT_ . $href->nodeValue,
                        'url' => Main::self()->getAssetPath(array('path' => $keyName, 'file' => $file))
                    );
                } elseif (substr($href->nodeValue, 0, 14) === "/vendor/asset/") {
                    $file = substr($href->nodeValue, 14);
                    $links[] = array(
                        'type' => $type,
                        'file' => _ROOT_ . $href->nodeValue,
                        'url' => Main::self()->getAssetPath(array('path' => 'vendor', 'file' => $file))
                    );
                }
            }
        }
        if (count($links) == 1) {
            $write .= sprintf('<link rel="stylesheet" href="%s">', $links[0]['url']);
        } elseif (count($links) > 1) {
            $source_config = var_export($links, true);
            $pathInfo = explode("@", trim($urlpath, "'"));
            if (isset($pathInfo[1])) {
                $name = $pathInfo[0];
                $file = trim($pathInfo[1], "'") . ".merge.css";
            } else {
                $name = $runName;
                $file = trim($urlpath, "'") . ".merge.css";
            }
            $source_path = _ROOT_ . "/sources/assets/$name/" . str_replace("/", "-", ltrim($file, "/")) . ".php";
            if (!is_dir(dirname($source_path))) {
                mkdir(dirname($source_path), 0777, true);
            }
            file_put_contents($source_path, "<?php \n return " . $source_config . ";");
            $write .= sprintf('<link rel="stylesheet" href="%s">', Main::self()->getAssetPath(array('path' => $name, 'file' => $file)));
        }
        $scriptItems = $doc->getElementsByTagName('script');
        $srcs = array();
        for ($i = 0; $i < $scriptItems->length; $i++) {
            $href = $scriptItems->item($i)->attributes->getNamedItem("src");
            if ($href) {
                $type = substr(strrchr($href->nodeValue, "."), 1);
                if (substr($href->nodeValue, 0, 10) === "../Public/") {
                    $srcs[] = array(
                        'type' => $type,
                        'file' => $basepath . substr($href->nodeValue, 3),
                        'url' => Main::self()->getAssetPath(array('path' => $runName, 'file' => substr($href->nodeValue, 10)))
                    );
                } elseif (substr($href->nodeValue, 0, 5) === "/app/") {
                    $keys = explode("/", substr($href->nodeValue, 5));
                    $keyName = $keys[0] . "-" . $keys[2];
                    $file = substr($href->nodeValue, strlen($keyName) + 18);
                    $srcs[] = array(
                        'type' => $type,
                        'file' => _APP_ROOT_ . $href->nodeValue,
                        'url' => Main::self()->getAssetPath(array('path' => $keyName, 'file' => $file))
                    );
                } elseif (substr($href->nodeValue, 0, 14) === "/vendor/asset/") {
                    $file = substr($href->nodeValue, 14);
                    $srcs[] = array(
                        'type' => $type,
                        'file' => _ROOT_ . $href->nodeValue,
                        'url' => Main::self()->getAssetPath(array('path' => 'vendor', 'file' => $file))
                    );
                }
            }
        }
        if (count($srcs) == 1) {
            $write .= sprintf('<script type="text/javascript" src="%s"></script>', $srcs[0]['url']);
        } elseif (count($srcs) > 1) {
            $source_config = var_export($srcs, true);
            $pathInfo = explode("@", trim($urlpath, "'"));
            if (isset($pathInfo[1])) {
                $name = $pathInfo[0];
                $file = trim($pathInfo[1], "'") . ".merge.js";
            } else {
                $name = $runName;
                $file = trim($urlpath, "'") . ".merge.js";
            }
            $source_path = _ROOT_ . "/sources/assets/$name/" . str_replace("/", "-", ltrim($file, "/")) . ".php";
            if (!is_dir(dirname($source_path))) {
                mkdir(dirname($source_path), 0777, true);
            }
            file_put_contents($source_path, "<?php \n return " . $source_config . ";");
            $write .= sprintf('<script type="text/javascript" src="%s"></script>',
                Main::self()->getAssetPath(array('path' => $name, 'file' => $file)));
        }

        return $write;
    }

    public static function path($body, $scale) {
        return preg_replace_callback('# (src|href)(\s*=\s*["\'])([^"\']+)#', function ($match) use ($scale) {
            $file = "";
            $name = "";
            if (substr($match[3], 0, 10) === "../Public/") {
                $name = Main::self()->getRunApp()->getName() . "-" . Main::self()->getRunApp()->getTheme();
                $file = substr($match[3], 10);
            } elseif (substr($match[3], 0, 5) === "/app/") {
                $keys = explode("/", substr($match[3], 5));
                $name = $keys[0] . "-" . $keys[2];
                $file = substr($match[3], strlen($name) + 18);
            } elseif (substr($match[3], 0, 14) === "/vendor/asset/") {
                $name = "vendor";
                $file = substr($match[3], 14);
            }
            return " " . $match[1] . $match[2] . Main::self()->getAssetPath(array("path" => $name, "file" => $file)) . "?scale=" . $scale;
        }, $body);
    }

    protected static function minifyCss(Request $request, Response $response, $name, $file, $file_path) {
        $lastTime = $request->getServerParam('HTTP_IF_MODIFIED_SINCE');
        $lastEtag = $request->getServerParam('HTTP_IF_NONE_MATCH');
        if (($lastTime || $lastEtag) && is_file($file_path)) {
            $last_modified = filemtime($file_path);
            $etag = md5_file($file_path);

            $headers = array("Content-Type" => "text/css");
            if (Main::self()->getEnv() != "prod") {
                $headers['Last-Modified'] = gmdate("D, d M Y H:i:s", strtotime($last_modified)) . " GMT";
                $headers['Etag'] = $etag;
                $headers['Cache-Control'] = "max-age=0";
            } else {
                $headers['Cache-Control'] = "max-age=31536000";
            }
            $response->setHeaders($headers);
            if (strtotime($lastTime) == $last_modified || trim($lastEtag) == $etag) {
                return $response->withStatus(304);
            } else {
                $response->write(file_get_contents($file_path));
                return $response;
            }
        }
        if ($name == "vendor") {
            $base = _VENDOR_ROOT_ . "/asset/";
        } else {
            list($app, $theme) = explode("-", $name);
            $base = _APP_ROOT_ . "/" . ucfirst($app) . "/View/" . $theme . "/Public";
        }
        $baseurl = Main::self()->getAssetPath(array("path" => $name, "file" => $file));
        if (".less.css" == strtolower(substr($file, -9))) {
            $file = $base . substr($file, 0, -4);
            $Less = new LessPrepare($file);
            $content = $Less->getString();
        } elseif (".scss.css" == strtolower(substr($file, -9))) {
            $file = $base . substr($file, 0, -4);
            $scss = new SassPrepare($file);
            $content = $scss->getString();
        } elseif (".merge.css" == substr($file, -10)) {
            $configs_file = _ROOT_ . "/sources/assets/$name/" . str_replace("/", "-", ltrim($file, "/")) . ".php";
            if (!is_file($configs_file)) {
                return $response;
            }
            $configs = include $configs_file;
            $content = "";
            foreach ($configs as $config) {
                if ($config['type'] == "scss") {
                    $scss = new SassPrepare($config['file']);
                    $fileContent = $scss->getString();
                } elseif ($config['type'] == "less") {
                    $Less = new LessPrepare($config['file']);
                    $fileContent = $Less->getString();
                } else {
                    $fileContent = file_get_contents($config['file']);
                }
                if (dirname($config['url']) != $baseurl) {
                    $fileContent = preg_replace_callback('#url\s*\(([^\)]+)\)#i', function ($matches) use ($config) {
                        return Assetic::assetUrlReplace($matches, $config);
                    }, $fileContent);
                }
                $content .= $fileContent;
            }

        } else {
            $content = file_get_contents($base . $file);
        }
        if (!(Main::self()->isDebug())) {
            $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
            $content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);
        }
        if (!is_dir(dirname($file_path))) {
            mkdir(dirname($file_path), 0777, true);
        }
        file_put_contents($file_path, $content);
        $last_modified = filemtime($file_path);
        $etag = md5_file($file_path);

        $headers = array("Content-Type" => "text/css");
        if (Main::self()->getEnv() != "prod") {
            $headers['Last-Modified'] = gmdate("D, d M Y H:i:s", strtotime($last_modified)) . " GMT";
            $headers['Etag'] = $etag;
            $headers['Cache-Control'] = "max-age=0";
        } else {
            $headers['Cache-Control'] = "max-age=31536000";
        }
        $response->setHeaders($headers);

        $response->write($content);
        return $response;
    }

    protected static function minifyJs(Request $request, Response $response, $name, $file, $file_path) {
        $lastTime = $request->getServerParam('HTTP_IF_MODIFIED_SINCE');
        $lastEtag = $request->getServerParam('HTTP_IF_NONE_MATCH');
        if (($lastTime || $lastEtag) && is_file($file_path)) {
            $last_modified = filemtime($file_path);
            $etag = md5_file($file_path);
            $headers = array("Content-Type" => "application/javascript");
            if (Main::self()->getEnv() != "prod") {
                $headers['Last-Modified'] = gmdate("D, d M Y H:i:s", strtotime($last_modified)) . " GMT";
                $headers['Etag'] = $etag;
                $headers['Cache-Control'] = "max-age=0";
            } else {
                $headers['Cache-Control'] = "max-age=31536000";
            }
            $response->setHeaders($headers);
            if (strtotime($lastTime) == $last_modified || trim($lastEtag) == $etag) {
                return $response->withStatus(304);
            } else {
                $response->write(file_get_contents($file_path));
                return $response;
            }
        }
        if ($name == "vendor") {
            $base = _VENDOR_ROOT_ . "/asset/";
        } else {
            list($app, $theme) = explode("-", $name);
            $base = _APP_ROOT_ . "/" . ucfirst($app) . "/View/" . $theme . "/Public";
        }
        if (".merge.js" == substr($file, -9)) {
            $configs_file = _ROOT_ . "/sources/assets/$name/" . str_replace("/", "-", ltrim($file, "/")) . ".php";
            if (!is_file($configs_file)) {
                return $response;
            }
            $configs = include $configs_file;
            $content = "";
            foreach ($configs as $config) {
                $content .= file_get_contents($config['file']) . "\n";
            }
        } else {
            $content = file_get_contents($base . $file);
        }
        if (!(Main::self()->isDebug())) {
            $content = JSMin::minify($content);
        }
        if (!is_dir(dirname($file_path))) {
            mkdir(dirname($file_path), 0777, true);
        }
        file_put_contents($file_path, $content);
        $last_modified = filemtime($file_path);
        $etag = md5_file($file_path);

        $headers = array("Content-Type" => "application/javascript");
        if (Main::self()->getEnv() != "prod") {
            $headers['Last-Modified'] = gmdate("D, d M Y H:i:s", strtotime($last_modified)) . " GMT";
            $headers['Etag'] = $etag;
            $headers['Cache-Control'] = "max-age=0";
        } else {
            $headers['Cache-Control'] = "max-age=31536000";
        }
        $response->setHeaders($headers);
        $response->write($content);
        return $response;
    }

    public static function asset(Request $request, Response $response, $path) {
        $pos = strpos($path, "/");
        $name = substr($path, 0, $pos);
        $file = substr($path, $pos);
        if ($name == "upload") {
            return self::upload($request, $response, $name, $file);
        }
        $file_path = _ROOT_ . "/sources/assets/public/" . $path;
        if (substr($path, -4) == ".css") {
            return self::minifyCss($request, $response, $name, $file, $file_path);
        } elseif (substr($path, -3) == ".js") {
            return self::minifyJs($request, $response, $name, $file, $file_path);
        }
        return self::media($request, $response, $name, $file, $file_path);
    }

    private static function upload(Request $request, Response $response, $name, $file) {
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
            "otf" => "",
            "eot" => "",
            "svg" => "",
            "ttf" => "",
            "woff" => "",
            "woff2" => "",
            "swf" => "application/x-shockwave-flash",
        );
        $base = _SOURCE_ROOT_ . "/upload/";
        $file = $base . $file;
        list($file) = explode("?", $file);
        list($file) = explode("#", $file);
        if (!is_file($file)) {
            $response->write("404 NOT FOUND");
            return $response->withStatus(404);
        }
        $extensions = array_keys($mime);
        $info = pathinfo($file);
        if (!in_array($info["extension"], $extensions)) {
            $response->withStatus(403);
            return $response;
        }
        $handle = fopen($file, "rb");
        $fstat = fstat($handle);
        if (in_array($info['extension'], explode("|", "jpg|jpeg|png|gif")) && isset($params["scale"]) && is_numeric($params["scale"])) {
            if ($params["scale"] == 0 || $params["scale"] == 100) {
                $response->setHeaders(array(
                    "Cache-Control" => "max-age=31536000",
                    "Content-type" => $mime[$info["extension"]],
                    "Content-Length" => $fstat["size"],
                ));
                $response->write(fread($handle, $fstat["size"]));
                return $response;
            } else {
                $imageInfo = getimagesize($file, $infos);
                if (!$imageInfo) {
                    $response->setHeaders(array(
                        "Cache-Control" => "max-age=31536000",
                        "Content-type" => $mime[$info["extension"]],
                    ));
                    $response->write(fread($handle, $fstat["size"]));
                    return $response;
                }
                $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
                if (!$imageType) {
                    $response->setHeaders(array(
                        "Cache-Control" => "max-age=31536000",
                        "Content-type" => $mime[$info["extension"]],
                    ));
                    $response->write(fread($handle, $fstat["size"]));
                    return $response;
                }
                if ($fstat['size'] < 81920) {
                    $response->setHeaders(array(
                        "Cache-Control" => "max-age=31536000",
                        "Content-type" => $mime[$info["extension"]],
                        "Content-Length" => $fstat["size"],
                    ));
                    $response->write(fread($handle, $fstat["size"]));
                    return $response;
                }
                $functionName = "imagecreatefrom" . $imageType;
                $sourImage = $functionName($file);
                ob_start();
                if ((int)$params["scale"] > 75) {
                    $level = 75;
                } elseif ((int)$params["scale"] < 15) {
                    $level = 15;
                } else {
                    $level = (int)$params["scale"];
                }
                imagejpeg($sourImage, null, $level);
                imagedestroy($sourImage);
                $response->setHeaders(array(
                    "Cache-Control" => "max-age=31536000",
                    "Content-type" => $mime[$info["extension"]],
                    "Content-Length" => ob_get_length(),
                ));
                $response->write(ob_get_clean());
                return $response;
            }
        }
        if (isset($mime[$info["extension"]]) && !empty($mime[$info["extension"]])) {
            $response->setHeaders(array(
                "Cache-Control" => "max-age=31536000",
                "Content-type" => $mime[$info["extension"]],
                "Content-Length" => $fstat["size"],
            ));
            $response->write(fread($handle, $fstat["size"]));
            return $response;
        }
        if (isset($mime[$info["extension"]])) {
            $response->setHeaders(array(
                "Cache-Control" => "max-age=31536000",
                "Content-Length" => $fstat["size"],
            ));
            $response->write(fread($handle, $fstat["size"]));
            return $response;
        }
        $response->setHeaders(array(
            "Cache-Control" => "Public",
            "Content-type" => "application/octet-stream",
            "Content-Disposition" => 'attachment; filename="' . $params["file"] . '"',
            "Content-Length" => $fstat["size"],
        ));
        $response->write(fread($handle, $fstat["size"]));
        return $response;
    }

    private static function media(Request $request, Response $response, $name, $file, $file_path) {
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
            "otf" => "",
            "eot" => "",
            "svg" => "",
            "ttf" => "",
            "woff" => "",
            "woff2" => "",
            "swf" => "application/x-shockwave-flash",
        );
        $params = $request->getQueryParams();
        if ($name == "vendor") {
            $base = _VENDOR_ROOT_ . "/asset/";
        } elseif ($name == "upload") {
            $base = _SOURCE_ROOT_ . "/upload/";
        } else {
            list($app, $theme) = explode("-", $name);
            $base = _APP_ROOT_ . "/" . ucfirst($app) . "/View/" . $theme . "/Public/";
        }
        $file = $base . $file;
        list($file) = explode("?", $file);
        list($file) = explode("#", $file);


        if (!is_file($file)) {
            $response->write("404 NOT FOUND");
            return $response->withStatus(404);
        }
        $extensions = array_keys($mime);
        $info = pathinfo($file);
        if (!in_array($info["extension"], $extensions)) {
            $response->withStatus(403);
            return $response;
        }
        $handle = fopen($file, "rb");
        $fstat = fstat($handle);
        if (in_array($info['extension'], explode("|", "jpg|jpeg|png|gif")) && isset($params["scale"]) && is_numeric($params["scale"])) {
            if ($params["scale"] == 0 || $params["scale"] == 100) {
                $response->setHeaders(array(
                    "Cache-Control" => "max-age=31536000",
                    "Content-type" => $mime[$info["extension"]],
                    "Content-Length" => $fstat["size"],
                ));
                $response->write(fread($handle, $fstat["size"]));
                return $response;
            } else {
                $imageInfo = getimagesize($file, $infos);
                if (!$imageInfo) {
                    $response->setHeaders(array(
                        "Cache-Control" => "max-age=31536000",
                        "Content-type" => $mime[$info["extension"]],
                    ));
                    $response->write(fread($handle, $fstat["size"]));
                    return $response;
                }
                $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
                if (!$imageType) {
                    $response->setHeaders(array(
                        "Cache-Control" => "max-age=31536000",
                        "Content-type" => $mime[$info["extension"]],
                    ));
                    $response->write(fread($handle, $fstat["size"]));
                    return $response;
                }
                if ($fstat['size'] < 81920) {
                    $response->setHeaders(array(
                        "Cache-Control" => "max-age=31536000",
                        "Content-type" => $mime[$info["extension"]],
                        "Content-Length" => $fstat["size"],
                    ));
                    $response->write(fread($handle, $fstat["size"]));
                    return $response;
                }
                $functionName = "imagecreatefrom" . $imageType;
                $sourImage = $functionName($file);
                ob_start();
                if ((int)$params["scale"] > 75) {
                    $level = 75;
                } elseif ((int)$params["scale"] < 15) {
                    $level = 15;
                } else {
                    $level = (int)$params["scale"];
                }
                imagejpeg($sourImage, null, $level);
                imagedestroy($sourImage);
                $response->setHeaders(array(
                    "Cache-Control" => "max-age=31536000",
                    "Content-type" => $mime[$info["extension"]],
                    "Content-Length" => ob_get_length(),
                ));
                $response->write(ob_get_clean());
                return $response;
            }
        }
        if (isset($mime[$info["extension"]]) && !empty($mime[$info["extension"]])) {
            $response->setHeaders(array(
                "Cache-Control" => "max-age=31536000",
                "Content-type" => $mime[$info["extension"]],
                "Content-Length" => $fstat["size"],
            ));
            $response->write(fread($handle, $fstat["size"]));
            return $response;
        }
        if (isset($mime[$info["extension"]])) {
            $response->setHeaders(array(
                "Cache-Control" => "max-age=31536000",
                "Content-Length" => $fstat["size"],
            ));
            $response->write(fread($handle, $fstat["size"]));
            return $response;
        }
        $response->setHeaders(array(
            "Cache-Control" => "Public",
            "Content-type" => "application/octet-stream",
            "Content-Disposition" => 'attachment; filename="' . $params["file"] . '"',
            "Content-Length" => $fstat["size"],
        ));
        $response->write(fread($handle, $fstat["size"]));
        return $response;
    }

    public static function assetUrlReplace($matches, $config) {
        $url = trim(trim(trim($matches[1]), '"'), "'");
        if ($url{0} == "/" || in_array(substr($url, 0, 4), array("data", "http"))) {
            return $matches[0];
        }
        return "url('" . self::url($config['url'], $url) . "')";
    }

    private static function url($path, $url) {
        $path = dirname($path) . "/" . ltrim($url, "/");
        $realPath = str_replace("/./", "/", $path);
        do {
            if (false === ($pos = strpos($realPath, "/../"))) {
                break;
            }
            $leftPath = substr($realPath, 0, $pos);
            $rightPaht = substr($realPath, $pos + 3);
            $realPath = dirname($leftPath) . $rightPaht;
        } while ($realPath);
        return preg_replace("/\/{2,}/", "/", $realPath);
    }

}