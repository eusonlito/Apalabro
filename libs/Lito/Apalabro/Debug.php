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

        if ($info) {
            $debug = array_reverse(debug_backtrace());

            echo '<pre>';

            foreach ($debug as $row) {
                echo "\n".$row['file'].' ['.$row['line'].']';
            }

            echo "\n\n";

            print_r($text);

            echo '</pre>';
        } else {
            echo "\n".'<pre>'; print_r($text); echo '</pre>'."\n";
        }
    }

    public function setDebug ($enable)
    {
        $this->enable = $enable;
    }
}
