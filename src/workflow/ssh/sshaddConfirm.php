<?php

require $_SERVER['ROOT'] . '/vendor/autoload.php';

$targetSsh = json_decode($argv[1], true);
\App\ssh\SSHManager::add($targetSsh);

echo $targetSsh['ssh'];
