<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader.php');

$Theme->set('body', basename(__FILE__));

$Theme->meta('title', __('Home'));

include ($Theme->get('base.php'));
