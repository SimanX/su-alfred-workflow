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
        'autocomplete' => $arg ?? $title
    ];
}

function exitWithResult()
{
    global $result;
    exit(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

date_default_timezone_set('Asia/Shanghai');
if (isset($argv[1])) {
    if (is_numeric($argv[1])) {
        $datetime = date('Y-m-d H:i:s', $argv[1]);
        addItem('shanghai_datetime', $datetime, date_default_timezone_get() . '时间');
    } else {
        $time = strtotime($argv[1]);
        if ($time == null) {
            addItem('error', '无效输入', '错误');
            exitWithResult();
        }
        addItem('shanghai_time', $time, date_default_timezone_get() . '时间戳');
    }
} else {
    $datetime = date('Y-m-d H:i:s');
    addItem('shanghai_datetime', $datetime, date_default_timezone_get() . '时间');
    addItem('shanghai_time', strtotime($datetime), date_default_timezone_get() . '时间戳');
    addItem('shanghai_microtime', intval(microtime(true) * 1000), date_default_timezone_get() . '毫秒级时间戳');
}

exitWithResult();
