<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Debug {
    private $enable = false;

    public function show ($text, $info = true)
    {
        if (!$this->enable) {
            return true;
        }

        $debug = debug_backtrace();

        if ($info) {
            echo "\n".'<pre>['.$debug[0]['file'].' - '.$debug[0]['line'].'] '; print_r($text); echo '</pre>'."\n";
        } else {
            echo "\n".'<pre>'; print_r($text); echo '</pre>'."\n";
        }
    }

    public function setDebug ($debug)
    {
        $this->debug = $debug;
    }
}
