<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Util\Xml;

/**
 * Description of xmlParse
 *
 * @author river2liu
 */
class XmlParse {

    static function xmlLoad($input) {
        $xmlDOM = simplexml_load_file($input, "SimpleXMLElement", LIBXML_NOCDATA);
        return self::parserecursive($xmlDOM);
    }

    static function xmlLoadString($input) {
        $xmlDOM = simplexml_load_string($input, "SimpleXMLElement", LIBXML_NOCDATA);
        return self::parserecursive($xmlDOM);
    }

    static function parserecursive($xmlARR) {
        return json_decode(json_encode($xmlARR), TRUE);
    }

    static function dump(array $array, $dom = 0, $item = 0) {
        if (!$dom) {
            $dom = new \DOMDocument("1.0");
        }
        if (!$item) {
            $item = $dom->createElement("root");
            $dom->appendChild($item);
        }
        foreach ($array as $key => $val) {
            $itemx = $dom->createElement(is_string($key) ? $key : "item");
            $item->appendChild($itemx);
            if (!is_array($val)) {
                $text = $dom->createTextNode($val);
                $itemx->appendChild($text);
            } else {
                self::dump($val, $dom, $itemx);
            }
        }
        return $dom->saveXML();
    }

}
