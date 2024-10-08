<?php

require $_SERVER['ROOT'] . '/vendor/autoload.php';

$keyword = \App\Facade\Facade::argument() ?? null;
$sshs = \App\ssh\SSHManager::getConfig();

if ($keyword !== null) {
	$sshs = array_filter($sshs, fn($ssh) => strpos($ssh['ssh'], $keyword) !== false || (isset($ssh['name']) && strpos($ssh['name'], $keyword) !== false));
}

foreach ($sshs as $ssh) {
	\App\Facade\Facade::addItem('删除ssh: ' . json_encode($ssh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $ssh['ssh'], $ssh['ssh']);
}

\App\Facade\Facade::output();