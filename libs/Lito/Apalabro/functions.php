<?php
/**
* phpCan - http://idc.anavallasuiza.com/
*
* phpCan is released under the GNU Affero GPL version 3
*
* More information at license.txt
*/

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

function timeAgo ($timestamp)
{
    $timestamp = preg_match('/^[0-9]+$/', $timestamp) ? $timestamp : strtotime($timestamp);

    return date('c', $timestamp);
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

function isAjax ()
{
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strcasecmp('XMLHttpRequest', $_SERVER['HTTP_X_REQUESTED_WITH']) === 0)) {
        return true;
    } else {
        return false;
    }
}

function getPlayDates ($games)
{
    if (!is_array($games)) {
        return array();
    }

    $games = array_values($games);
    $return = array();

    foreach ($games as $Game) {
        if (is_array($Game)) {
            $return = $return + getPlayDates($Game);
        } else if (isset($Game->last_turn->play_date)) {
            $return[$Game->id] = $Game->last_turn->play_date;
        }
    }

    return $return;
}

function getLastTurnMessage ($Game, $Current = null) {
    if (!is_object($Game) || (isset($Current->{$Game->id}) && ($Game->last_turn->play_date === $Current->{$Game->id}))) {
        return false;
    }

    if (in_array($Game->game_status, array('PENDING_MY_APPROVAL', 'PENDING_FIRST_MOVE'), true)) {
        return __('%s wants to play with you', $Game->opponent->name);
    }

    if ($Game->game_status === 'ACTIVE') {
        switch ($Game->last_turn->type) {
            case 'PLACE_TILE':
                return __('%s has place tiles', $Game->opponent->name);

            case 'PASS':
                return __('%s has passed the turn', $Game->opponent->name);

            case 'SWAP':
                return __('%s has swapped tiles', $Game->opponent->name);

            default:
                return __('%s has updated the game', $Game->opponent->name);
        }
    }

    if ($Game->game_status === 'ENDED') {
        if ($Game->ended_reason === 'NORMAL') {
            if ($Game->last_turn->type === 'REJECT') {
                return __('%s does\'t wants to play', $Game->opponent->name);
            } else if ($Game->last_turn->type === 'RESIGN') {
                return __('%s has resigned. You win!', $Game->opponent->name);
            } else if ($Game->win) {
                return __('Game versus %s was ended. You win!', $Game->opponent->name);
            } else {
                return __('Game versus %s was ended. You lost :(', $Game->opponent->name);
            }
        } else if ($Game->ended_reason === 'EXPIRE') {
            if ($Game->win) {
                return __('Game versus %s was expired. You win!', $Game->opponent->name);
            } else {
                return __('Game versus %s was expired. You lost :(', $Game->opponent->name);
            }
        }
    }

    return false;
}