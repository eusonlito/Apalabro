<?php
namespace Lito\Apalabro;

defined('BASE_PATH') or die();

class Curl {
    private $connection;
    private $server = '';
    private $headers = false;
    private $response;
    private $info;
    private $cache = false;

    private $Timer;

    public $Cache;
    public $Debug;

    public function init ($server)
    {
        global $Timer;

        $this->Timer = $Timer;

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

        $this->Cache = new Cache;
        $this->Debug = new Debug;
    }

    public function setOption ($option, $value)
    {
        curl_setopt($this->connection, $option, $value);
    }

    public function fullGet ($url)
    {
        $server = $this->server;
        $info = parse_url($url);

        $this->server = $info['scheme'].'://'.$info['host'];

        $response = $this->get($info['path'].(isset($info['query']) ? ('?'.$info['query']) : ''));

        $this->server = $server;

        return $response;
    }

    public function get ($url, $post = false, $cache = false)
    {
        $cache = (!$post && ($cache || $this->cache));

        if ($cache && $this->Cache->exists($url)) {
            return $this->Cache->get($url);
        }

        $this->Timer->mark('INI: Curl->get');

        $remote = $this->server.$url;

        $this->Debug->show('Connection to <strong>'.$remote.'</strong>');

        curl_setopt($this->connection, CURLOPT_URL, $remote);

        $this->response = curl_exec($this->connection);
        $this->info = curl_getinfo($this->connection);

        if (!$this->response) {
            return '';
        }

        $html = json_decode(preg_replace('/>\s+</', '><', str_replace(array("\n", "\r", "\t"), '', $this->response)));

        if ($cache) {
            $this->Cache->set($url, $html);
        }

        $this->Timer->mark('END: Curl->get');

        return $html;
    }

    public function post ($url, $data)
    {
        curl_setopt($this->connection, CURLOPT_POST, true);
        curl_setopt($this->connection, CURLOPT_POSTFIELDS, (is_array($data) ? json_encode($data) : $data));

        $html = $this->get($url, true);

        curl_setopt($this->connection, CURLOPT_POST, false);

        return $html;
    }

    public function custom ($request, $url, $data = array())
    {
        curl_setopt($this->connection, CURLOPT_CUSTOMREQUEST, $request);

        if ($data) {
            curl_setopt($this->connection, CURLOPT_POSTFIELDS, (is_array($data) ? json_encode($data) : $data));
        }

        $html = $this->get($url, true);

        curl_setopt($this->connection, CURLOPT_CUSTOMREQUEST, false);

        return $html;
    }

    public function getInfo ()
    {
        return $this->info;
    }

    public function getResponse ()
    {
        return $this->response;
    }

    public function setCookie ($value)
    {
        curl_setopt($this->connection, CURLOPT_COOKIE, $value);
    }
}
