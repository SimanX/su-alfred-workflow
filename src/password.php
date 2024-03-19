<?php
function randomChar()
{
    static $strings = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    return substr($strings, random_int(0, strlen($strings) - 1), 1);
}

function randomPassword($length = 16)
{
    $result = '';
    for ($idx = 0; $idx < $length; $idx++) {
        $result .= randomChar();
    }

    return $result;
}

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

$length = $argv[1] ?? 16;
if ($length > 256) {
    addItem('error', '错误:', '长度不能超过256');
    exitWithResult();
}

for ($num = 0; $num < 10; $num++) {
    addItem('password', randomPassword($length), '随机密码');
}

exitWithResult();
