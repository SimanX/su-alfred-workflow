<?php

require $_SERVER['ROOT'] . '/vendor/autoload.php';

$input = \facade\Facade::argument();

\facade\Facade::addItem(json_decode("\"$input\""), 'decode');
\facade\Facade::addItem(substr(json_encode($input), 1, -1), 'encode');
\facade\Facade::output();
