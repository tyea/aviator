<?php

namespace Tyea\Aviator;

use Exception;

class Env
{
	private function __construct()
	{
	}

	private static $filename;

	public static function configure(string $filename): void
	{
		Env::$filename = $filename;
	}

	private static $vars;

	private static function vars(): array
	{
		if (!Env::$vars) {
			if (!Env::$filename) {
				throw new Exception();
			}
			Env::$vars = parse_ini_file(Env::$filename, false, INI_SCANNER_RAW) ?: [];
		}
		return Env::$vars;
	}

	public static function get(string $name, string $default = null): ?string
	{
		return Env::vars()[$name] ?? $default;
	}
}