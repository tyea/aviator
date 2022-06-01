<?php

use Tyea\Aviator\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as RequestFactory;
use Tyea\Aviator\Response;
use Tyea\Aviator\Template;
use Tyea\Aviator\App;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface as SessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Tyea\Aviator\CollectionFactory;
use Symfony\Component\Validator\Validation as ValidatorFactory;
use Symfony\Component\PropertyAccess\PropertyAccess as PropertyAccessorFactory;
use DateTimeZone as DateTimeTimeZone;
use Tyea\Aviator\MySql;
use Tyea\Aviator\Redis;
use Symfony\Component\HttpClient\CurlHttpClient as Curl;

function env(string $key, mixed $default = null): mixed
{
	return $_ENV[$key] ?? $default;
}

function request(): Request
{
	$request = Container::get("Request");
	if (!$request) {
		$request = RequestFactory::createFromGlobals();
		Container::set("Request", $request);
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
	$template = Container::get("Template");
	if (!$template) {
		$template = new Template();
		Container::set("Template", $template);
	}
	return $template;
}

function app(): App
{
	$app = Container::get("App");
	if (!$app) {
		$app = new App();
		Container::set("App", $app);
	}
	return $app;
}

function session(array $options = [], SessionStorage $sessionStorage = null): Session
{
	$session = Container::get("Session");
	if (!$session) {
		if (!$sessionStorage) {
			$sessionStorage = new NativeSessionStorage($options);
		}
		$session = new Session($sessionStorage);
		Container::set("Session", $session);
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
	return new DateTime("now", new DateTimeTimeZone($timeZone));
}

function mysql(): MySql
{
	$mysql = Container::get("MySql");
	if (!$mysql) {
		$mysql = new MySql();
		Container::set("MySql", $mysql);
	}
	return $mysql;
}

function redis(): Redis
{
	$redis = Container::get("Redis");
	if (!$redis) {
		$redis = new Redis();
		Container::set("Redis", $redis);
	}
	return $redis;
}

function curl(array $options = []): Curl
{
	return new Curl($options);
}
