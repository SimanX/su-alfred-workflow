<?php

require $_SERVER['ROOT'] . '/vendor/autoload.php';

$input = \App\Facade\Facade::argument();

\App\Facade\Facade::addItem(json_decode("\"$input\""), 'decode');
\App\Facade\Facade::addItem(substr(json_encode($input), 1, -1), 'encode');
\App\Facade\Facade::output();
