<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader-min.php');

$git = array();

if (is_dir(BASE_PATH.'.git')) {
    $cmd = exec('which git');

    if ($cmd) {
        $log = explode("\n", shell_exec('cd "'.BASE_PATH.'"; '.$cmd.' log -10 --pretty=format:"%s|%ci"'));

        foreach ($log as $row) {
            preg_match('/^([^\|]*)\|(.*)$/', $row, $matches);

            $git[] = array(
                'message' => $matches[1],
                'date' => humanDate($matches[2])
            );
        }

        $git = array_chunk($git, 5);
    }
}

$Theme->set('body', basename(__FILE__));

$Theme->meta('title', __('About'));

include ($Theme->get('base.php'));
