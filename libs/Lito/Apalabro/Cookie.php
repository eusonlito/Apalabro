<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Cookie {
    private $name = 'apalabro';
    private $expire = 2592000; // 30 days

    public function set ($values, $time = null)
    {
        $cookie = $this->get();

        foreach ($values as $key => $value) {
            if ($value) {
                $cookie[$key] = $value;
            } else {
                unset($cookie[$key]);
            }
        }

        if ($cookie) {
            $cookie = gzdeflate(serialize($cookie));
            $time = time() + ($time ?: $this->expire);
        } else {
            $time = time() - 3600;
        }

        return setCookie($this->name, $cookie, $time);
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
