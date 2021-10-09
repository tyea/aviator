<?php

use Tyea\Aviator\Http;

function dd($expression): void
{
	ob_start();
	var_dump($expression);
	$output = ob_get_clean();
	$content = "<pre>" . htmlspecialchars($output, ENT_COMPAT, "UTF-8") . "</pre>";
	Http::response($content, 500);
}

function config(string $key, $default = null)
{
	global $_CONFIG;
	return $_CONFIG[$key] ?? $default;
}

function now(): DateTime
{
	return new DateTime("now", new DateTimeZone("UTC"));
}