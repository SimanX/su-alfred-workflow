<?php
/**
 * @author xushao
 * @description
 */

namespace App\ssh;

class SSHManager
{
	private static ?SSHManager $instance = null;

	public static function getInstance()
	{
		if (!static::$instance) {
			static::$instance = new SSHManager();
			static::init();
		}

		return static::$instance;
	}

	private static function init()
	{
		$configFile = static::configPath();
		$pathInfo = pathinfo($configFile);
		if (!file_exists($dir = $pathInfo['dirname'])) {
			mkdir($dir);
		}

		if (!file_exists($configFile)) {
			$file = fopen($configFile, "w") or die("Unable to open file!");
			fclose($file);
		}
	}

	public static function getConfig()
	{
		$instance = static::getInstance();
		$configRawContent = file_get_contents(static::configPath());
		if (!$configRawContent) {
			return [];
		}

		return json_decode($configRawContent, true) ?? [];
	}

	public static function configPath()
	{
		return $_SERVER['HOME'] . '/com.simanx.alfred.ssh/ssh_config.json';
	}

	// 添加配置
	public static function add($target)
	{
		$instance = static::getInstance();
		$config = $instance::getConfig();
		$targetIdx = null;
		foreach ($config as $idx => $ssh) {
			if ($ssh['ssh'] == $target['ssh']) {
				// ssh一样，但是名字不一样
				$targetIdx = $idx;
				break;
			}

			if (isset($target['name']) && isset($ssh['name']) &&
				$target['name'] == $ssh['name']) {
				// 名字一样，ssh不一样
				$targetIdx = $idx;
				break;
			}
		}

		if ($targetIdx !== null) {
			// 修改
			$config[$targetIdx] = $target;
		} else {
			$config[] = $target;
		}

		$instance::setConfig($config);
	}

	public static function setConfig($config)
	{
		$instance = static::getInstance();
		$configPath = $instance::configPath();
		file_put_contents($configPath, json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
	}

	public static function remove($ssh)
	{
		$instance = static::getInstance();
		$sshConfig = $instance::getConfig();
		$sshConfig = array_filter($sshConfig, function ($item) use ($ssh) {
			return $item['ssh'] != $ssh;
		});

		sort($sshConfig);
		$instance::setConfig($sshConfig);
	}
}