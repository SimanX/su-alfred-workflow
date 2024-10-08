<?php

require $_SERVER['ROOT'] . '/vendor/autoload.php';

use Alfred\Workflows\Workflow;


$url = \facade\Facade::argument();
$urlInfo = parse_url($url);
if ($urlInfo === false) {
	\facade\Facade::error('无效的url', 'error');
}

$workflow = \facade\Facade::getInstance();
$url = urldecode($url);
$workflow->item()
	->title($url)
	->subtitle('decode')
	->arg($url);

$queryArray = [];
if (isset($urlInfo['query'])) {
	$queryString = $urlInfo['query'];
	parse_str($queryString, $queryArray);
	$workflow->item()
		->title($queryString)
		->subtitle('query')
		->arg($queryString);

	$queryJson = json_encode($queryArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	$workflow->item()
		->title($queryJson)
		->subtitle('query(json)')
		->arg($queryJson);
}

if (isset($urlInfo['scheme'])) {
	$scheme = $urlInfo['scheme'];
	$workflow->item()
		->title($scheme)
		->subtitle('协议')
		->arg($scheme);
}

if (isset($urlInfo['host'])) {
	$host = $urlInfo['host'];
	$workflow->item()
		->title($host)
		->subtitle('host')
		->arg($host);
}

if (isset($urlInfo['port'])) {
	$port = $urlInfo['port'];
	$workflow->item()
		->title($port)
		->subtitle('端口');
}

if (isset($urlInfo['path'])) {
	$path = $urlInfo['path'];
	$workflow->item()
		->title($path)
		->subtitle('path')
		->arg($path);
}

$content = json_encode($urlInfo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$workflow->item()
	->title($content)
	->subtitle('所有解析内容')
	->arg($content);

$workflow->output();