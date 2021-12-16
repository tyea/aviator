<?php

namespace Tyea\Aviator;

class Container
{
	private static $values = [];

	public static function get(string $name): mixed
	{
		return Container::$values[$name] ?? null;
	}

	public static function set(string $name, mixed $value): void
	{
		Container::$values[$name] = $value;
	}
}