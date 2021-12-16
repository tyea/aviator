<?php

use Tyea\Aviator\Container;
use Symfony\Component\Routing\RouteCollection as Routes;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext as Context;
use Symfony\Component\Routing\Matcher\UrlMatcher as Matcher;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;
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

function route(string|array $methods, string $path, callable $callable): void
{
	$routes = Container::get("ROUTES");
	if (!$routes) {
		$routes = new Routes();
		Container::set("ROUTES", $routes);
	}
	if (!is_array($methods)) {
		$methods = [$methods];
	}
	$name = implode(",", $methods) . "_" . $path;
	$route = new Route($path);
	$route->setMethods($methods);
	$route->addDefaults(["_callable" => $callable]);
	$routes->add($name, $route);
}

function fallback(callable $callable): void
{
	Container::set("FALLBACK", $callable);
}

function start(): void
{
	$context = new Context();
	$context->fromRequest(request());
	$matcher = new Matcher(Container::get("ROUTES"), $context);
	try {
		$match = $matcher->match(request()->getPathInfo());
		$callable = $match["_callable"];
		$args = array_filter(
			$match,
			function ($value, $key) {
				return $key != "_callable";
			},
			ARRAY_FILTER_USE_BOTH
		);
	} catch (RoutingException $exception) {
		$callable = Container::get("FALLBACK");
		$args = [];
	}
	try {
		call_user_func(Container::get("BEFORE"));
		call_user_func_array($callable, $args);
	} catch (Exception $exception) {
		call_user_func_array(Container::get("ERROR"), [$exception]);
	}
}

function before(callable $callable): void
{
	Container::set("BEFORE", $callable);
}

function error(callable $callable): void
{
	Container::set("ERROR", $callable);
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

function redis(): Redis
{
	$redis = Container::get("REDIS");
	if (!$redis) {
		$redis = new Redis();
		Container::set("REDIS", $redis);
	}
	return $redis;
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