<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace phpex\Helper\Form;

/**
 * Description of StrFilter
 *
 * @author Administrator
 */
class StrFilter {

    static function word($s) {
        return preg_replace("[^a-zA-Z0-9_]", "", $s);
    }

    static function html($s, $quotes = ENT_QUOTES) {
        return htmlSpecialChars($s, $quotes);
    }

    public static function HtmlComment($s) {
        $s = (string) $s;
        if ($s && ($s[0] === '-' || $s[0] === '>' || $s[0] === '!')) {
            $s = ' ' . $s;
        }
        return str_replace('-', '- ', $s); // dash is very problematic character in comments
    }

    public static function xml($s) {
        // XML 1.0: \x09 \x0A \x0D and C1 allowed directly, C0 forbidden
        // XML 1.1: \x00 forbidden directly and as a character reference,
        //   \x09 \x0A \x0D \x85 allowed directly, C0, C1 and \x7F allowed as character references
        return htmlSpecialChars(preg_replace('#[\x00-\x08\x0B\x0C\x0E-\x1F]+#', '', $s), ENT_QUOTES);
    }

    static function css($s) {
        return addcslashes($s, "\x00..\x1F!\"#$%&'()*+,./:;<=>?@[\\]^`{|}~");
    }

    public static function js($s) {
        if ($s instanceof IHtmlString || $s instanceof \Nette\Utils\IHtmlString) {
            $s = $s->__toString(TRUE);
        }

        $json = json_encode($s, PHP_VERSION_ID >= 50400 ? JSON_UNESCAPED_UNICODE : 0);
        if ($error = json_last_error()) {
            throw new \RuntimeException(PHP_VERSION_ID >= 50500 ? json_last_error_msg() : 'JSON encode error', $error);
        }

        return str_replace(array("\xe2\x80\xa8", "\xe2\x80\xa9", ']]>', '<!'), array('\u2028', '\u2029', ']]\x3E', '\x3C!'), $json);
    }

    public static function url($s) {
        return preg_match('~^(?:(?:https?|ftp)://[^@]+(?:/.*)?|mailto:.+|[/?#].*|[^:]+)\z~i', $s) ? $s : '';
    }

    public static function attributes($attrs) {
        if (!is_array($attrs)) {
            return '';
        }

        $s = '';
        foreach ($attrs as $key => $value) {
            if ($value === NULL || $value === FALSE) {
                continue;
            } elseif ($value === TRUE) {
                if (static::$xhtml) {
                    $s .= ' ' . $key . '="' . $key . '"';
                } else {
                    $s .= ' ' . $key;
                }
                continue;
            } elseif (is_array($value)) {
                $tmp = NULL;
                foreach ($value as $k => $v) {
                    if ($v != NULL) { // intentionally ==, skip NULLs & empty string
                        //  composite 'style' vs. 'others'
                        $tmp[] = $v === TRUE ? $k : (is_string($k) ? $k . ':' . $v : $v);
                    }
                }
                if ($tmp === NULL) {
                    continue;
                }

                $value = implode($key === 'style' || !strncmp($key, 'on', 2) ? ';' : ' ', $tmp);
            } else {
                $value = (string) $value;
            }

            $q = strpos($value, '"') === FALSE ? '"' : "'";
            $s .= ' ' . $key . '=' . $q . str_replace(array('&', $q), array('&amp;', $q === '"' ? '&quot;' : '&#39;'), $value) . $q;
        }
        return $s;
    }

}
