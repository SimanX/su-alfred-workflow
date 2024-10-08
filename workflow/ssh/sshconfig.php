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
        // "type": "file",
        'title' => $title,
        'subtitle' => $subtitle,
        'arg' => $arg,
        'mods' => [
            "cmd" => [
                "valid" => true,
                "arg" => $arg,
                "subtitle" => '在finder中查看'
            ]
        ]
    ];
}

function exitWithResult()
{
    global $result;
    exit(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

addItem('ssh_config', $_SERVER['HOME'] . '/com.simanx.alfred.ssh/ssh_config.json', '打开配置文件');
exitWithResult();