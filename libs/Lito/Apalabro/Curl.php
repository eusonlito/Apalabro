<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Curl {
    private $connection;
    private $server = '';
    private $headers = false;
    private $response;

    public $Cache;
    public $Debug;

    public function init ($server)
    {
        $this->server = $server;
        $this->connection = curl_init();

        curl_setopt($this->connection, CURLOPT_REFERER, $this->server);
        curl_setopt($this->connection, CURLOPT_FAILONERROR, true);

        if (!ini_get('open_basedir') && (strtolower(ini_get('safe_mode')) !== 'on')) {
            curl_setopt($this->connection, CURLOPT_FOLLOWLOCATION, true);
        }

        curl_setopt($this->connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->connection, CURLINFO_HEADER_OUT, true);
        curl_setopt($this->connection, CURLOPT_TIMEOUT, 10);
        curl_setopt($this->connection, CURLOPT_USERAGENT, 'Android/4.0.4 Apalabro/1.4.1.3');

        curl_setopt($this->connection, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Connection: Keep-Alive'
        ));

        if ($this->headers) {
            curl_setopt($this->connection, CURLOPT_HEADER, true);
            curl_setopt($this->connection, CURLOPT_VERBOSE, true);
        }

        $this->Cache = new \Lito\Apalabro\Cache;
        $this->Debug = new \Lito\Apalabro\Debug;
    }

    public function get ($url, $post = false)
    {
        if (!$post && $this->Cache->exists($url)) {
            return $this->Cache->get($url);
        }

        $remote = $this->server.$url;

        $this->Debug->show('Connection to <strong>'.$remote.'</strong>');

        curl_setopt($this->connection, CURLOPT_URL, $remote);

        $this->response = curl_exec($this->connection);

        if (!$this->response) {
            return '';
        }

        $html = json_decode(preg_replace('/>\s+</', '><', str_replace(array("\n", "\r", "\t"), '', $this->response)));

        if (!$post) {
            $this->Cache->set($url, $html);
        }

        return $html;
    }

    public function post ($url, $data)
    {
        $cache_key = func_get_args();

        if ($this->Cache->exists($cache_key)) {
            return $this->Cache->get($cache_key);
        }

        curl_setopt($this->connection, CURLOPT_POST, true);
        curl_setopt($this->connection, CURLOPT_POSTFIELDS, json_encode($data));

        $html = $this->get($url, true);

        curl_setopt($this->connection, CURLOPT_POST, false);

        $this->Cache->set($cache_key, $html);

        return $html;
    }

    public function setCookie ($value)
    {
        curl_setopt($this->connection, CURLOPT_COOKIE, $value);
    }
}
