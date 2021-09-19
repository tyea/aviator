<?php

namespace Tyea\Aviator;

use Twig\Loader\FilesystemLoader as Loader;
use Twig\Environment as Engine;
use Exception;

class Tpl
{
	private function __construct()
	{
	}

	private static $directory;
	private static $options;

	public static function configure(string $directory, array $options = []): void
	{
		Tpl::$directory = $directory;
		Tpl::$options = $options;
	}

	private static $engine;

	private static function engine(): Engine
	{
		if (!Tpl::$engine) {
			if (!Tpl::$directory) {
				throw new Exception();
			}
			$loader = new Loader(Tpl::$directory);
			Tpl::$engine = new Engine($loader, Tpl::$options);
		}
		return Tpl::$engine;
	}

	public static function render(string $template, array $data = []): string
	{
		return Tpl::engine()->render($template, $data);
	}
}