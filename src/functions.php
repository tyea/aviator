<?php

use Tyea\Aviator\Registry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Request as RequestFactory;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Tyea\Aviator\CollectionFactory;
use Symfony\Component\Validator\Validation as ValidatorFactory;
use Symfony\Component\PropertyAccess\PropertyAccess as PropertyAccessorFactory;
use Tyea\Aviator\Response;
use Tyea\Aviator\MySql;
use DateTimeZone as DateTimeTimeZone;
use Tyea\Aviator\Smtp;
use Symfony\Component\HttpClient\CurlHttpClient as Curl;
use Tyea\Aviator\Redis;
use Tyea\Aviator\Template;

function env(string $key, mixed $default = null): mixed
{
	return $_ENV[$key] ?? $default;
}

function request(): Request
{
	$request = Registry::get("REQUEST");
	if (!$request) {
		$request = RequestFactory::createFromGlobals();
		Registry::set("REQUEST", $request);
	}
	return $request;
}

function session(array $options = []): Session
{
	$session = Registry::get("SESSION");
	if (!$session) {
		$session = new Session(new NativeSessionStorage($options));
		Registry::set("SESSION", $session);
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

function response(): Response
{
	return new Response();
}

function dd(mixed $expression): void
{
	ob_start();
	var_dump($expression);
	$content = ob_get_clean();
	response()->raw($content, 500, ["Content-Type" => "text/plain"]);
}

function mysql(): MySql
{
	$mysql = Registry::get("MYSQL");
	if (!$mysql) {
		$mysql = new MySql();
		Registry::set("MYSQL", $mysql);
	}
	return $mysql;
}

function now(string $timeZone = "UTC"): DateTime
{
	return new DateTime("now", new DateTimeTimeZone($timeZone));
}

function smtp(): Smtp
{
	$smtp = Registry::get("SMTP");
	if (!$smtp) {
		$smtp = new Smtp();
		Registry::set("SMTP", $smtp);
	}
	return $smtp;
}

function curl(array $options = []): Curl
{
	return new Curl($options);
}

function redis(): Redis
{
	$redis = Registry::get("REDIS");
	if (!$redis) {
		$redis = new Redis();
		Registry::set("REDIS", $redis);
	}
	return $redis;
}

function template(): Template
{
	$template = Registry::get("TEMPLATE");
	if (!$template) {
		$template = new Template();
		Registry::set("TEMPLATE", $template);
	}
	return $template;
}