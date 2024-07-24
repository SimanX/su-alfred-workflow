<?php
error_reporting(E_ERROR);

class StringRandomer
{
    private string $chars;
    public static $complex = 0;
    public function __construct($specificChars)
    {
        $this->chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($specificChars != 'easy') {
            $this->chars .= '~!@#$%^&*()_+{}|:<>?-=';

            if ($specificChars) {
                // 其他字符串则排除其中字符
                $this->chars = str_replace($specificChars, '', $this->chars);
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

$arg = $argv[1] ?? '16';
[$length, $specificChars] = explode(' ', $arg);
if (!is_int($length)) {
    $specificChars = $length;
    $length = 16;
}

if ($length > 256) {
    addItem('error', '错误:', '长度不能超过256');
    exitWithResult();
}

$randomer = new StringRandomer($specificChars ?? "");
for ($num = 0; $num < 10; $num++) {
    addItem('password', $randomer->random($length), '随机密码');
}

exitWithResult();
