<?php

use Tyea\Aviator\App;
use Twig\Loader\FilesystemLoader as Loader;
use Twig\Environment as Engine;

function env(string $key, $default = null)
{
	return $_ENV[$key] ?? $default;
}

function dd($var = null): void
{
	ob_start();
	var_dump($var);
	$dump = ob_get_clean();
	$content = "<pre>" . htmlspecialchars($dump, ENT_COMPAT, "UTF-8") . "</pre>";
	App::response($content, 500);
}

function render(string $template, array $data = []): string
{
	$loader = new Loader(env("TEMPLATES_DIRECTORY", __DIR__ . "/../../../.."));
	$environment = new Engine($loader, env("TEMPLATES_OPTIONS", []));
	return $environment->render($template, $data);
}