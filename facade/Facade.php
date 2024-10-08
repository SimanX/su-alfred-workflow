<?php
/**
 * @author xushao
 * @description
 */

namespace facade;

use Alfred\Workflows\Workflow;

/**
 * Class Facade
 * Workflow 的门面封装
 * @package facade
 * @method static argument(): ?string
 * @method static item(): \Alfred\Workflows\Item
 * @method static output()
 * @method static env(string $key = null, $default = null): string|null|array
 */
class Facade
{
	protected static ?Workflow $instance = null;

	public static function getInstance()
	{
		if (!static::$instance) {
			static::$instance = new Workflow();
		}

		return static::$instance;
	}

	public static function __callStatic($method, $arguments)
	{
		$instance = static::getInstance();
		if (!$instance) {
			throw new \Exception('facade root has not been set.');
		}

		return $instance->$method(...$arguments);
	}

	public static function error(string $title, string $subtitle, string $arg = null)
	{
		$instance = static::getInstance();
		$item = $instance->item()
			->title($title)
			->subtitle($subtitle);
		if ($arg) {
			$item->arg($arg);
		}

		$instance->output();
		exit();
	}

	public static function exit(string $title = null, string $subtitle = null, string $arg = null)
	{
		if (!$title) {
			static::getInstance()->output();
			exit();
		}

		$arg ??= $title;
		static::error($title, $subtitle, $arg);
	}

	public static function addItem(string $title, string $subtitle, string $arg = null)
	{
		$arg ??= $title;
		$instance = static::getInstance();
		$instance->item()
			->title($title)
			->subtitle($subtitle)
			->arg($arg);
	}
}