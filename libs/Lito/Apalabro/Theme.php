<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Theme {
    private $theme = 'default';
    private $path;
    private $www;
    private $templates;
    private $message;

    public function __construct ()
    {
        $this->setTheme($this->theme);
    }

    public function set ($key, $template)
    {
        $this->templates[$key] = $template;
    }

    public function get ($key)
    {
        if (isset($this->templates[$key]))
        {
            return $this->path.$this->templates[$key];
        } else {
            return $this->path.$key;
        }
    }

    public function getTheme ()
    {
        return $this->theme;
    }

    public function setTheme ($theme)
    {
        $path = BASE_PATH.'themes/'.$theme.'/';

        if (!is_dir($path)) {
            throw new \Exception(sprintf('Theme path "%s" does not exists.', $path));
        }

        $this->www = BASE_WWW.'themes/'.$theme.'/';
        $this->path = $path;
        $this->theme = $theme;
    }

    public function setMessage ($text, $status)
    {
        $this->message = array(
            'text' => $text,
            'status' => $status
        );
    }

    public function getMessage ()
    {
        return $this->message;
    }

    public function www ()
    {
        return $this->www;
    }

    public function HumaDate ($timestamp)
    {
        $timestamp = preg_match('/^[0-9]+$/', $timestamp) ? $timestamp : strtotime($timestamp);
        // Get time difference and setup arrays
        $difference = time() - $timestamp;
        $periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'years');
        $lengths = array('60','60','24','7','4.35','12');

        // Past or present
        if ($difference >= 0) {
            $ending = 'ago';
        } else {
            $difference = -$difference;
            $ending = 'to go';
        }
     
        // Figure out difference by looping while less than array length
        // and difference is larger than lengths.
        $arr_len = count($lengths);

        for ($j = 0; ($j < $arr_len) && ($difference >= $lengths[$j]); $j++) {
            $difference /= $lengths[$j];
        }
     
        // Round up     
        $difference = round($difference);
     
        // Make plural if needed
        if ($difference != 1) {
            $periods[$j] .= 's';
        }

        // Default format
        $text = $difference.' '.$periods[$j].' '.$ending;
     
        // over 24 hours
        if ($j > 2) {
            // future date over a day formate with year
            if ($ending === 'to go') {
                if (($j === 3) && ($difference === 1)) {
                    $text = 'Tomorrow at '. date('g:i a', $timestamp);
                } else {
                    $text = date('F j, Y \a\t g:i a', $timestamp);
                }

                return $text;
            }
     
            if (($j === 3) && ($difference === 1)) {
                $text = 'Yesterday at '. date('g:i a', $timestamp);
            } else if ($j === 3) {
                $text = date('l \a\t g:i a', $timestamp);
            } else if (($j < 6) && !(($j === 5) && ($difference === 12))) {
                $text = date('F j \a\t g:i a', $timestamp);
            } else {
                $text = date('F j, Y \a\t g:i a', $timestamp);
            }
        }
     
        return $text;
    }
}
