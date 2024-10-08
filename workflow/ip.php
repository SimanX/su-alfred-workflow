<?php
error_reporting(E_ERROR);

require $_SERVER['ROOT'] . '/vendor/autoload.php';

function is_private_ip($ip)
{
	$private_ips = [
		['192.168.0.0', '192.168.255.255'], // Private IPv4 address range for 192.168.x.x
		['10.0.0.0', '10.255.255.255'], // Private IPv4 address range for 10.x.x.x
		['172.16.0.0', '172.31.255.255'], // Private IPv4 address range for 172.16.x.x to 172.31.x.x
		['fc00::', 'fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff'], // Private IPv6 address range for fc00::/7
		['fe80::', 'febf:ffff:ffff:ffff:ffff:ffff:ffff:ffff'] // Link-local IPv6 address range for fe80::/10
	];

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

$queryIp = \facade\Facade::argument() ?? '';
$ipService = \facade\Facade::env('ipservice');
if (empty($queryIp)) {
	// 没有输入ip，查询本机ip信息
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
			\facade\Facade::addItem($interface['ipv4'], sprintf('内网 ipv4(%s)', $name));
		}
	}

	// 获取外网ipv4和ipv6值
	$externalIpv4Result = shell_exec('curl -4 -s ' . $ipService . '?lang=zh-CN');
	if (!empty($externalIpv4Result)) {
		$externalIpv4Json = json_decode($externalIpv4Result, true);
		if (isset($externalIpv4Json['query'])) {
			\facade\Facade::addItem($externalIpv4Json['query'], '外网ipv4');
		}
	}

	foreach ($interfaces as $name => $interface) {
		if (isset($interface['ipv6'])) {
			\facade\Facade::addItem($interface['ipv6'], sprintf('内网 ipv6(%s)', $name));
		}
	}

	$externalIpv6Result = shell_exec('curl -6 -s ' . $ipService);
	if (!empty($externalIpv6Result)) {
		$externalIpv6Json = json_decode($externalIpv6Result, true);
		if ($externalIpv6Json && isset($externalIpv6Json['query'])) {
			\facade\Facade::addItem($externalIpv6Json['query'], '外网ipv6');
		}
	}
	\facade\Facade::exit();
}

if (in_array($queryIp, ['127.0.0.1', 'localhost'])) {
	\facade\Facade::exit('queryresult', '本机ip');
}

if (is_private_ip($queryIp)) {
	\facade\Facade::exit('queryresult', '局域网');
}

$apiUrl = 'https://qifu.baidu.com/ip/geo/v1/district?ip=';
$ipQueryResult = file_get_contents($apiUrl . $queryIp);
if (empty($ipQueryResult)) {
	\facade\Facade::error('查询ip归属地失败，请检查网络和ip拼写', '错误');
}

$ipQueryJson = json_decode($ipQueryResult, true);
if (strtoupper($ipQueryJson['code']) != 'SUCCESS') {
	\facade\Facade::addItem('查询失败: ' . $ipQueryJson['msg'], '错误');
	\facade\Facade::exit(json_encode($ipQueryJson, JSON_UNESCAPED_UNICODE), '详细结果json');
}

$data = $ipQueryJson['data'];
\facade\Facade::addItem(sprintf('国家: %s, 省份: %s, 城市: %s, 区: %s',
	$data['country'], $data['prov'], $data['city'], $data['district'] ?? 'null'), '查询结果');
\facade\Facade::exit(json_encode($ipQueryJson, JSON_UNESCAPED_UNICODE), '详细结果json');