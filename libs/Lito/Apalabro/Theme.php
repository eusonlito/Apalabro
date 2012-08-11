<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Theme {
    private $theme = 'default';
    private $path;
    private $www;
    private $templates;
    private $message;
    private $meta;

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
        if (isset($this->templates[$key])) {
            return $this->path.$this->templates[$key];
        } else if (is_file($this->path.$key)) {
            return $this->path.$key;
        } else {
            return $this->path.'empty.html';
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
            throw new \Exception(__('Theme path "%s" does not exists.', $path));
        }

        $this->www = BASE_WWW.'themes/'.$theme.'/';
        $this->path = $path;
        $this->theme = $theme;
    }

    public function setMessage ($text, $status, $back = false)
    {
        $this->message = array(
            'text' => $text,
            'status' => $status,
            'back' => $back
        );
    }

    public function getMessage ()
    {
        return $this->message;
    }

    public function path ()
    {
        return $this->path;
    }

    public function www ()
    {
        return $this->www;
    }

    public function meta ($tag, $value = '')
    {
        if ($value) {
            $this->meta[$tag] = $value;
        } else {
            return $this->meta[$tag];
        }
    }
}
