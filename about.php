<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader-min.php');

$Theme->set('body', basename(__FILE__));

$Theme->meta('title', __('About'));

include ($Theme->get('base.php'));
