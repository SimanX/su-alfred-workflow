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

$targetSsh = json_decode($argv[1], true);
$sshConfig = json_decode(file_get_contents($sshConfigFile), true) ?? [];
function updateSshConfig()
{
    global $sshConfigFile, $sshConfig;
    file_put_contents($sshConfigFile, json_encode($sshConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}

if (!in_array($targetSsh, $sshConfig)) {
    $targetIdx = null;
    foreach ($sshConfig as $idx => $ssh) {
        if ($ssh['ssh'] == $targetSsh['ssh']) {
            $targetIdx = $idx;
            break;
        }

        if (isset($targetSsh['name']) && isset($ssh['name']) &&
            $targetSsh['name'] == $ssh['name']) {
            $targetIdx = $idx;
            break;
        }
    }

    if ($targetIdx !== null) {
        $sshConfig[$targetIdx] = $targetSsh;
        updateSshConfig();
    } else {
        $sshConfig[] = $targetSsh;
        updateSshConfig();
    }
}

echo $targetSsh['ssh'];
