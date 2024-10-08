<?php

require $_SERVER['ROOT'] . '/vendor/autoload.php';

date_default_timezone_set('Asia/Shanghai');
$timezone = date_default_timezone_get();
$input = \App\Facade\Facade::argument();
if (!$input) {
	$workflow = \App\Facade\Facade::getInstance();
	$datetime = date('Y-m-d H:i:s');
	$workflow->item()
		->title($datetime)
		->subtitle($timezone . '时间')
		->arg($datetime);

	$time = strtotime($datetime);
	$workflow->item()
		->title($time)
		->subtitle('时间戳')
		->arg($time);

	$microTime = intval(microtime(true) * 1000);
	$workflow->item()
		->title($microTime)
		->subtitle($timezone . '毫秒级时间戳')
		->arg($microTime);
	$workflow->output();
	exit();
}

if (filter_var($input, FILTER_VALIDATE_INT)) {
	// 时间戳转换为时间
	$datetime = date('Y-m-d H:i:s', $input);
	\App\Facade\Facade::exit($datetime, $timezone . '时间');
}

$time = strtotime($input);
if ($time == null) {
	\App\Facade\Facade::error('无效输入', '错误');
}

// 时间转换为时间戳
\App\Facade\Facade::exit($time, $timezone . '时间戳');
