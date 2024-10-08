<?php

$result = [
    'items' => []
];

function addItem($uid, $title, $subtitle, $arg = null)
{
    global $result;
    $arg = $arg ?? $title;
    $result['items'][] = [
        'uid' => $uid,
        'title' => $title,
        'subtitle' => $subtitle,
        'arg' => $arg,
        'autocomplete' => $arg
    ];
}

function exitWithResult()
{
    global $result;
    exit(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

$sshConfigFile = $_SERVER['HOME'] . '/com.simanx.alfred.ssh/ssh_config.json';
$pathInfo = pathinfo($sshConfigFile);
if (!file_exists($dir = $pathInfo['dirname'])) {
    mkdir($dir);
}

if (!file_exists($sshConfigFile)) {
    $file = fopen($sshConfigFile, "w") or die("Unable to open file!");
    fclose($file);
}

$keyword = $argv[1] ?? null;
$sshConfig = json_decode(file_get_contents($sshConfigFile), true) ?? [];
foreach ($sshConfig as $ssh) {
    if($keyword === null) {
        addItem('connect' . $ssh['ssh'], json_encode($ssh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            '连接ssh: ' . json_encode($ssh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $ssh['ssh']);
    }else if (strpos($ssh['ssh'], $keyword) !== false ||
        (isset($ssh['name']) && strpos($ssh['name'], $keyword) !== false)) {
        addItem('connect' . $ssh['ssh'], json_encode($ssh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            '连接ssh: ' . json_encode($ssh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $ssh['ssh']);
    }
}

exitWithResult();
