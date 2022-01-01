<?php

use Tyea\Aviator\Globals;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as RequestFactory;
use Tyea\Aviator\Response;
use Tyea\Aviator\Template;
use Tyea\Aviator\App;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Tyea\Aviator\CollectionFactory;
use Symfony\Component\Validator\Validation as ValidatorFactory;
use Symfony\Component\PropertyAccess\PropertyAccess as PropertyAccessorFactory;
use DateTimeZone as TimeZone;
use Tyea\Aviator\MySql;
use Tyea\Aviator\Redis;
use Tyea\Aviator\Smtp;
use Symfony\Component\HttpClient\CurlHttpClient as Curl;

function env(string $key, mixed $default = null): mixed
{
	return $_ENV[$key] ?? $default;
}

function request(): Request
{
	$request = Globals::get("REQUEST");
	if (!$request) {
		$request = RequestFactory::createFromGlobals();
		Globals::set("REQUEST", $request);
	}
	return $request;
}

function response(): Response
{
	return new Response();
}

function dd(mixed ...$vars): void
{
	ob_start();
	foreach ($vars as $var) {
		var_dump($var);
	}
	$content = ob_get_clean();
	response()->raw($content, 500, ["Content-Type" => "text/plain"]);
}

function template(): Template
{
	$template = Globals::get("TEMPLATE");
	if (!$template) {
		$template = new Template();
		Globals::set("TEMPLATE", $template);
	}
	return $template;
}

function app(): App
{
	$app = Globals::get("APP");
	if (!$app) {
		$app = new App();
		Globals::set("APP", $app);
	}
	return $app;
}

function session(array $options = []): Session
{
	$session = Globals::get("SESSION");
	if (!$session) {
		$session = new Session(new NativeSessionStorage($options));
		Globals::set("SESSION", $session);
	}
	return $session;
}

function validate(array $data, array $constraints, bool $allowMissingFields = false, bool $allowExtraFields = false): array
{
	$collection = CollectionFactory::createFromArray($constraints, $allowMissingFields, $allowExtraFields);
	$validator = ValidatorFactory::createValidator();
	$violations = $validator->validate($data, $collection);
	$errors = [];
	if ($violations) {
		$propertyAccessor = PropertyAccessorFactory::createPropertyAccessor();
		foreach ($violations as $violation) {
			$propertyAccessor->setValue($errors, $violation->getPropertyPath(), $violation->getMessage());
		}
	}
	return $errors;
}

function now(string $timeZone = "UTC"): DateTime
{
	return new DateTime("now", new TimeZone($timeZone));
}

function mysql(): MySql
{
	$mysql = Globals::get("MYSQL");
	if (!$mysql) {
		$mysql = new MySql();
		Globals::set("MYSQL", $mysql);
	}
	return $mysql;
}

function redis(): Redis
{
	$redis = Globals::get("REDIS");
	if (!$redis) {
		$redis = new Redis();
		Globals::set("REDIS", $redis);
	}
	return $redis;
}

function smtp(): Smtp
{
	$smtp = Globals::get("SMTP");
	if (!$smtp) {
		$smtp = new Smtp();
		Globals::set("SMTP", $smtp);
	}
	return $smtp;
}

function curl(array $options = []): Curl
{
	return new Curl($options);
}