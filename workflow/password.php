<?php
error_reporting(E_ERROR);

require_once $_SERVER['ROOT'] . '/vendor/autoload.php';

class StringRandomer
{
	private string $chars;

	public function __construct($specificChars)
	{
		$this->chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ($specificChars != 'easy') {
			$this->chars .= '~!@#$%^&*()_+{}|:<>?-=';

			if ($specificChars) {
				// 其他字符串则排除其中字符
				for ($idx = 0; $idx < strlen($specificChars); $idx++) {
					$this->chars = str_replace($specificChars[$idx], '', $this->chars);
				}
			}
		}
	}

	public function random($length = 16)
	{
		$result = '';
		for ($idx = 0; $idx < $length; $idx++) {
			$result .= substr($this->chars, random_int(0, strlen($this->chars) - 1), 1);
		}

		return $result;
	}
}

$argv = \facade\Facade::argument() ?? '16';
[$length, $specificChars] = explode(' ', $argv);
if (!filter_var($length, FILTER_VALIDATE_INT)) {
	$specificChars = $length;
	$length = 16;
}

if ($length > 256) {
	\facade\Facade::error('长度不能超过256', '错误');
}

$randomer = new StringRandomer($specificChars ?? "");
for ($num = 0; $num < 10; $num++) {
	$pwd = $randomer->random($length);
	\facade\Facade::addItem($pwd, '随机密码');
}

\facade\Facade::output();