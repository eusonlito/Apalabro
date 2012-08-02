<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Cache {
    private $expire = 2592000;
    private $enabled = true;
    private $reload = false;
    private $folder = 'cache/';

    public function __construct ()
    {
        if (!$this->enabled) {
            return true;
        }

        $this->folder = BASE_PATH.$this->folder;

        if (!is_dir($this->folder) || !is_writable($this->folder)) {
            $this->enabled = false;
        }
    }

    private function file ($key)
    {
        $file = md5(serialize($key));

        return $this->folder.chunk_split($file, 4, '/').$file;
    }

    public function enabled () {
        return $this->enabled;
    }

    public function exists ($key)
    {
        if (!$this->enabled || $this->reload) {
            return false;
        }

        $file = $this->file($key);

        return (is_file($file) && (filemtime($file) > time())) ? true : false;
    }

    public function set ($key, $value, $expire = 0)
    {
        if (!$this->enabled) {
            return false;
        }

        $file = $this->file($key);

        if (!is_dir(dirname($file))) {
            mkdir(dirname($file), 0700, true);
        }

        file_put_contents($file, gzdeflate(serialize($value)));

        chmod($file, 0600);

        touch($file, time() + ($expire ?: $this->expire));

        return $value;
    }

    public function get ($key)
    {
        if (!$this->enabled) {
            return false;
        }

        $file = $this->file($key);

        if (!is_file($file) || (filemtime($file) < time())) {
            return null;
        }

        return unserialize(gzinflate(file_get_contents($file)));
    }

    public function reload ()
    {
        if (!$this->enabled) {
            return false;
        }

        $this->reload = true;
    }
}
