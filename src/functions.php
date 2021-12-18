<?php

use Tyea\Aviator\Container;
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
use DateTimeZone as DateTimeTimeZone;
use Tyea\Aviator\MySql;
use Tyea\Aviator\Smtp;
use Symfony\Component\HttpClient\CurlHttpClient as Curl;

function env(string $key, mixed $default = null): mixed
{
	return $_ENV[$key] ?? $default;
}

function request(): Request
{
	$request = Container::get("REQUEST");
	if (!$request) {
		$request = RequestFactory::createFromGlobals();
		Container::set("REQUEST", $request);
	}
	return $request;
}

function response(): Response
{
	$response = Container::get("RESPONSE");
	if (!$response) {
		$response = new Response();
		Container::set("RESPONSE", $response);
	}
	return $response;
}

function dd(mixed $expression): void
{
	ob_start();
	var_dump($expression);
	$content = ob_get_clean();
	response()->raw($content, 500, ["Content-Type" => "text/plain"]);
}

function template(): Template
{
	$template = Container::get("TEMPLATE");
	if (!$template) {
		$template = new Template();
		Container::set("TEMPLATE", $template);
	}
	return $template;
}

function app(): App
{
	$app = Container::get("APP");
	if (!$app) {
		$app = new App();
		Container::set("APP", $app);
	}
	return $app;
}

function session(array $options = []): Session
{
	$session = Container::get("SESSION");
	if (!$session) {
		$session = new Session(new NativeSessionStorage($options));
		Container::set("SESSION", $session);
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
	$mysql = Container::get("MYSQL");
	if (!$mysql) {
		$mysql = new MySql();
		Container::set("MYSQL", $mysql);
	}
	return $mysql;
}

function smtp(): Smtp
{
	$smtp = Container::get("SMTP");
	if (!$smtp) {
		$smtp = new Smtp();
		Container::set("SMTP", $smtp);
	}
	return $smtp;
}

function curl(array $options = []): Curl
{
	return new Curl($options);
}