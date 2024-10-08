<?php

require $_SERVER['ROOT'] . '/vendor/autoload.php';

$input = \App\Facade\Facade::argument();
$result = md5($input);
\App\Facade\Facade::exit($result, 'md5');