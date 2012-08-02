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
            throw new \Exception(__('Theme path "%s" does not exists.', $path));
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
}
