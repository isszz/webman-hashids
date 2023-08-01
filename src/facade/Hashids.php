<?php
declare (strict_types = 1);

namespace isszz\hashids\facade;

class Hashids
{
	protected static ?\isszz\hashids\Hashids $_instance = null;

	public static function instance()
	{
		if (!static::$_instance) {
			static::$_instance = new \isszz\hashids\Hashids;
		}

		return static::$_instance;
	}

	public static function __callStatic($name, $arguments)
	{
		return static::instance()->{$name}(... $arguments);
	}
}
