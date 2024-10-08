<?php

use App\Facade\Facade;

require $_SERVER['ROOT'] . '/vendor/autoload.php';

$files = glob($_SERVER['HOME'] . '/.ssh/*.pub');
foreach ($files as $file) {
	Facade::addItem(file_get_contents($file), $file);
}

Facade::output();
