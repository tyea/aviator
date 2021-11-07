<?php

namespace Tyea\Aviator;

class Registry
{
	private static $objects = [];

	public static function get(string $name): ?object
	{
		return Registry::$objects[$name] ?? null;
	}

	public static function set(string $name, ?object $object): void
	{
		Registry::$objects[$name] = $object;
	}
}