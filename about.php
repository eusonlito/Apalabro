<?php
require (__DIR__.'/libs/Lito/Apalabro/Loader-min.php');

$Theme->set('body', basename(__FILE__));

include ($Theme->get('base.php'));
