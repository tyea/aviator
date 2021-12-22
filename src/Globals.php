<?php

namespace Tyea\Aviator;

class Globals
{
	private function __construct()
	{
	}

	private static $values = [];

	public static function get(string $name): mixed
	{
		return Globals::$values[$name] ?? null;
	}

	public static function set(string $name, mixed $value): void
	{
		Globals::$values[$name] = $value;
	}
}