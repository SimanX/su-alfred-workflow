<?php

require $_SERVER['ROOT'] . '/vendor/autoload.php';

$input = \facade\Facade::argument();
$result = md5($input);
\facade\Facade::exit($result, 'md5');