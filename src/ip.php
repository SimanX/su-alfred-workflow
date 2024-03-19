<?php

$result = [
    'items' => []
];

function addItem($uid, $title, $subtitle = null, $arg = null)
{
    global $result;
    $item = [
        'uid' => $uid,
        // "type": "file",
        'title' => $title,
        'arg' => $arg ?? $title,
        'autocomplete' => $arg,
        // "icon": {
        //     "type": "fileicon",
        //     "path": "~/Desktop"
        // }
    ];

    if (!is_null($subtitle)) {
        $item['subtitle'] = $subtitle;
    }

    $result['items'][] = $item;
}

function exitWithResult()
{
    global $result;
    exit(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function is_private_ip($ip)
{
    $private_ips = array(
        array('192.168.0.0', '192.168.255.255'), // Private IPv4 address range for 192.168.x.x
        array('10.0.0.0', '10.255.255.255'), // Private IPv4 address range for 10.x.x.x
        array('172.16.0.0', '172.31.255.255'), // Private IPv4 address range for 172.16.x.x to 172.31.x.x
        array('fc00::', 'fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff'), // Private IPv6 address range for fc00::/7
        array('fe80::', 'febf:ffff:ffff:ffff:ffff:ffff:ffff:ffff') // Link-local IPv6 address range for fe80::/10
    );

    $long_ip = ip2long($ip);
    if ($long_ip !== false) {
        foreach ($private_ips as $range) {
            $start = ip2long($range[0]);
            $end = ip2long($range[1]);
            if ($long_ip >= $start && $long_ip <= $end) {
                return true;
            }
        }
    }

    return false;
}

$queryIp = $argv[1] ?? '';
if (in_array($queryIp, ['127.0.0.1', 'localhost'])) {
    addItem('queryresult', '本地ip');
    exitWithResult();
}

if (is_private_ip($queryIp)) {
    addItem('queryresult', '局域网');
    exitWithResult();
}

$ipService = getenv('ipservice');
if (empty($queryIp)) {
    exec('ifconfig', $output);
    $interfaces = [];
    $interface = '';
    foreach ($output as $line) {
        if (preg_match('/^([a-z0-9]+):/i', $line, $matches)) {
            $interface = $matches[1];
        }

        if (preg_match('/^\s+inet (\d+\.\d+\.\d+\.\d+)\s/', $line, $matches)) {
            $interfaces[$interface]['ipv4'] = $matches[1];
        }

        if (preg_match('/^\s+inet6 ([a-z0-9:]+)%/', $line, $matches)) {
            $interfaces[$interface]['ipv6'] = $matches[1];
        }

        if (preg_match('/^\s+status: ([a-z]+)/', $line, $matches)) {
            $interfaces[$interface]['status'] = $matches[1];
        }
    }

    $interfaces = array_filter($interfaces, function ($interface) {
        return (isset($interface['status']) && $interface['status'] == 'active') ||
            (isset($interface['ipv4']) && strpos($interface['ipv4'], '127.0.0.1') !== false);
    });

    foreach ($interfaces as $name => $interface) {
        if (isset($interface['ipv4'])) {
            addItem($interface['ipv4'], $interface['ipv4'], sprintf('内网 ipv4(%s)', $name));
        }
    }

    // 获取外网ipv4和ipv6值
    $externalIpv4Result = shell_exec('curl -4 -s ' . $ipService . '?lang=zh-CN');
    if (!empty($externalIpv4Result)) {
        $externalIpv4Json = json_decode($externalIpv4Result, true);
        if (isset($externalIpv4Json['query'])) {
            addItem($externalIpv4Json['query'], $externalIpv4Json['query'], '外网ipv4');
        }
    }

    foreach ($interfaces as $name => $interface) {
        if (isset($interface['ipv6'])) {
            addItem($interface['ipv6'], $interface['ipv6'], sprintf('内网 ipv6(%s)', $name));
        }
    }

    $externalIpv6Result = shell_exec('curl -6 -s ' . $ipService);
    if (!empty($externalIpv6Result)) {
        $externalIpv6Json = json_decode($externalIpv6Result, true);
        if ($externalIpv6Json && isset($externalIpv6Json['query'])) {
            addItem($externalIpv6Json['query'], $externalIpv6Json['query'], '外网ipv4');
        }
    }
}

if($queryIp) {
    $apiUrl = 'https://qifu.baidu.com/ip/geo/v1/district?ip=';
    $ipQueryResult = file_get_contents($apiUrl . $queryIp);
    if (!empty($ipQueryResult)) {
        $ipQueryJson = json_decode($ipQueryResult, true);
        if (strtoupper($ipQueryJson['code']) == 'SUCCESS') {
            $data = $ipQueryJson['data'];
            addItem('queryresult', sprintf('国家: %s, 省份: %s, 城市: %s, 区: %s', $data['country'], $data['prov'], $data['city'], $data['district'] ?? 'null'), '查询结果', $ipQueryResult);
        } else {
            addItem('error', '查询失败', '查询失败: ' . $ipQueryJson['msg']);
        }
    } else {
        addItem('error', '查询失败', '查询ip归属地失败，请检查网络和ip拼写');
    }
}

exitWithResult();
