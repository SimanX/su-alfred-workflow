<?php

use App\Facade\Facade;
use App\ssh\SSHManager;

require $_SERVER['ROOT'] . '/vendor/autoload.php';
Facade::exit(SSHManager::configPath(), '配置文件');