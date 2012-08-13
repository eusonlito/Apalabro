<?php
/**
* phpCan - http://idc.anavallasuiza.com/
*
* phpCan is released under the GNU Affero GPL version 3
*
* More information at license.txt
*/

defined('BASE_PATH') or die();

/*
 * function __ ($text, [$args = null])
 *
 * return string
 */
function __ ($text, $args = null)
{
    global $Gettext;

    $text = $Gettext->translate($text);

    if (is_null($args)) {
        return $text;
    } elseif (is_array($args)) {
        return vsprintf($text, $args);
    } else {
        $args = func_get_args();

        array_shift($args);

        return vsprintf($text, $args);
    }
}

/*
 * function __e ($text)
 *
 * echo string
 */
function __e ($text, $args = null)
{
    if (count(func_get_args()) > 2) {
        $args = func_get_args();

        array_shift($args);
    }

    echo __($text, $args);
}

function humanDate ($timestamp)
{
    $timestamp = preg_match('/^[0-9]+$/', $timestamp) ? $timestamp : strtotime($timestamp);

    $diff = time() - $timestamp;

    if ($diff === 0) {
        return __('just now');
    }

    $day_diff = floor($diff / 86400);

    if ($diff > 0) {
        if ($diff < 60) {
            return __('just now');
        } else if ($diff < 120) {
            return __('one minute ago');
        } else if ($diff < 3600) {
            return __('%s minutes ago', floor($diff / 60));
        } else if ($diff < 7200) {
            return __('one hour ago');
        } else if ($diff < 86400) {
            return __('%s hours ago', floor($diff / 3600));
        } else if($day_diff == 1) {
            return __('yesterday');
        } else if ($day_diff < 7) {
            return __('%s days ago', $day_diff);
        } else {
            return date('m F Y', $timestamp);
        }
    } else {
        $diff = abs($diff);
        $day_diff = floor($diff / 86400);

        if ($day_diff == 0) {
            if ($diff < 120) {
                return __('in a minute');
            } else if ($diff < 3600) {
                return __('in %s minutes', floor($diff / 60));
            } else if ($diff < 7200) {
                return __('in an hour');
            } else if ($diff < 86400) {
                return __('in %s hours', floor($diff / 3600));
            }
        }

        return date('m F Y', $timestamp);
    }
}

function str_split_unicode ($str, $l = 0)
{
    if ($l > 0) {
        $ret = array();
        $len = mb_strlen($str, 'UTF-8');

        for ($i = 0; $i < $len; $i += $l) {
            $ret[] = mb_substr($str, $i, $l, 'UTF-8');
        }

        return $ret;
    }

    return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
}

if (!function_exists('mb_substr_replace')) {
    function mb_substr_replace ($string, $replacement, $start, $length = null, $encoding = null)
    {
        $string_length = is_null($encoding) ? mb_strlen($string) : mb_strlen($string, $encoding);
        
        if ($start < 0) {
            $start = max(0, $string_length + $start);
        } else if ($start > $string_length) {
            $start = $string_length;
        }
        
        if ($length < 0) {
            $length = max(0, $string_length - $start + $length);
        } else if ((is_null($length) === true) || ($length > $string_length)) {
            $length = $string_length;
        }
        
        if (($start + $length) > $string_length) {
            $length = $string_length - $start;
        }
        
        if (is_null($encoding)) {
            return mb_substr($string, 0, $start) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length);
        }
        
        return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, $start + $length, $string_length - $start - $length, $encoding);
    }
}

if (!function_exists('mb_strtr')) {
    function mb_strtr ($str, $from, $to)
    {
      return str_replace(mb_str_split($from), mb_str_split($to), $str);
    }

    function mb_str_split ($str)
    {
        return preg_split('~~u', $str, null, PREG_SPLIT_NO_EMPTY);;
    }
}

function encode2utf ($string)
{
    if ((mb_detect_encoding($string) === 'UTF-8') && mb_check_encoding($string, 'UTF-8')) {
        return $string;
    } else {
        return utf8_encode($string);
    }
}
