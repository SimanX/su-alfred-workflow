<?php

$result = [
    'items' => []
];

function addItem($uid, $title, $subtitle, $arg = null)
{
    global $result;
    $result['items'][] = [
        'uid' => $uid,
        // "type": "file",
        'title' => $title,
        'subtitle' => $subtitle,
        'arg' => $arg ?? $title,
        // "icon": {
        //     "type": "fileicon",
        //     "path": "~/Desktop"
        // }
    ];
}

function exitWithResult()
{
    global $result;
    exit(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

$input = $argv[1];

addItem('unicode', json_decode("\"$input\""), 'decode');
addItem('unicode', substr(json_encode($input), 1, -1), 'encode');

exitWithResult();
