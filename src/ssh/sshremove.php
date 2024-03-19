<?php

$sshConfigFile = $_SERVER['HOME'] . '/com.simanx.alfred.ssh/ssh_config.json';
$pathInfo = pathinfo($sshConfigFile);
if (!file_exists($dir = $pathInfo['dirname'])) {
    mkdir($dir);
}

if (!file_exists($sshConfigFile)) {
    $file = fopen($sshConfigFile, "w") or die("Unable to open file!");
    fclose($file);
}

$ssh = $argv[1];
$sshConfig = json_decode(file_get_contents($sshConfigFile), true) ?? [];
$sshConfig = array_filter($sshConfig, function ($item) use ($ssh) {
    return $item['ssh'] != $ssh;
});

sort($sshConfig);
file_put_contents($sshConfigFile, json_encode($sshConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

echo $ssh;
