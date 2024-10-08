<?php

require $_SERVER['ROOT'] . '/vendor/autoload.php';

$ssh = \App\Facade\Facade::argument();
\App\ssh\SSHManager::remove($ssh);
echo $ssh;
