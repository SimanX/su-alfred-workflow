<?php

error_reporting(E_ERROR);

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
        'autocomplete' => $arg,
        // "icon": {
        //     "type": "fileicon",
        //     "path": "~/Desktop"
        // }
        'mods' => [
            "cmd" => [
                "valid" => true,
                "arg" => $arg,
                "subtitle" => '添加并打开ssh'
            ]
        ]
    ];
}

function exitWithResult()
{
    global $result;
    exit(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

[$ssh, $name] = explode(' ', $argv[1]);
$targetSsh = [
    'ssh' => $ssh
];

if ($name) {
    $targetSsh['name'] = $name;
}

addItem(
    'sshadd',
    json_encode($targetSsh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
    '添加: ' . json_encode($targetSsh, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
);

exitWithResult();
