<?php

error_reporting(E_ERROR);

use Alfred\Workflows\ItemParam\Mod;
use App\Facade\Facade;

require $_SERVER['ROOT'] . '/vendor/autoload.php';

[$ssh, $name] = explode(' ', Facade::argument());
$targetSsh = [
	'ssh' => $ssh
];

if ($name) {
	$targetSsh['name'] = $name;
}

$result = json_encode($targetSsh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
Facade::addItem($result, '添加: ' . $result)->mod(
	(new Mod('cmd'))
		->valid(true)
		->arg($result)
		->subtitle('添加并打开ssh')
);
Facade::output();
