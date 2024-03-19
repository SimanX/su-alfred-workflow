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

$url = $argv[1];
$urlInfo = parse_url($url);
if ($urlInfo === false) {
    addItem('error', '错误:', '无效的url');
    exitWithResult();
}

$url = urldecode($url);
addItem('decode', $url, 'urldecode');

$queryArray = [];
if (isset($urlInfo['query'])) {
    parse_str($urlInfo['query'], $queryArray);
    addItem('query', $urlInfo['query'], 'query');
    addItem('query(json)', json_encode($queryArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 'query(json)');
}

if (isset($urlInfo['scheme'])) {
    addItem('scheme', $urlInfo['scheme'], '协议');
}

if (isset($urlInfo['host'])) {
    addItem('host', $urlInfo['host'], 'host');
}

if (isset($urlInfo['port'])) {
    addItem('port', $urlInfo['port'], '端口');
}

if (isset($urlInfo['path'])) {
    addItem('path', $urlInfo['path'], 'path');
}

addItem('all', json_encode($urlInfo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), '所有解析内容');

exitWithResult();
