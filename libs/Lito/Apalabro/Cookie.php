<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Cookie {
    private $name = 'apalabro';
    private $expire = 2592000; // 30 days

    public function set ($value, $time = null)
    {
        $cookie = array_merge($this->get(), (array)$value);
        $time = time() + ($time ?: $this->expire);

        return setCookie($this->name, gzdeflate(serialize($cookie)), $time);
    }

    public function get ()
    {
        if (isset($_COOKIE[$this->name])) {
            return unserialize(gzinflate($_COOKIE[$this->name]));
        } else {
            return array();
        }
    }
}
