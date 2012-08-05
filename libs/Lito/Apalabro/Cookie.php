<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Cookie {
    private $name = 'apalabro';

    public function set ($value, $time = null)
    {
        $cookie = array_merge($this->get(), (array)$value);
        $time = $time ? (time() + $time) : $time;

        setCookie($this->name, gzdeflate(serialize($cookie)), $time);
    }

    public function get ()
    {
        if (isset($_COOKIE['apalabro'])) {
            return unserialize(gzinflate($_COOKIE[$this->name]));
        } else {
            return array();
        }
    }
}
