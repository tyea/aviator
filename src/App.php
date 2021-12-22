<?php

namespace Tyea\Aviator;

use Symfony\Component\Routing\RouteCollection as Routes;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext as Context;
use Symfony\Component\Routing\Matcher\UrlMatcher as Matcher;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;
use Exception;

class App
{
	private $before;

	function before(callable $callable): void
	{
		$this->before = $callable;
	}

	private $routes;

	function route(string $method, string $path, callable $callable): void
	{
		if (!$this->routes) {
			$this->routes = new Routes();
		}
		$route = new Route($path);
		$route->setMethods([$method]);
		$route->addDefaults(["_callable" => $callable]);
		$name = $method . "_" . $path;
		$this->routes->add($name, $route);
	}

	private $fallback;

	function fallback(callable $callable): void
	{
		$this->fallback = $callable;
	}

	private $error;

	function error(callable $callable): void
	{
		$this->error = $callable;
	}

	function start(): void
	{
		if (!$this->before || !$this->routes || !$this->fallback || !$this->error) {
			throw new Exception();
		}
		$context = new Context();
		$context->fromRequest(request());
		$matcher = new Matcher($this->routes, $context);
		try {
			$match = $matcher->match(request()->getPathInfo());
			list($callable, $args) = $this->parseMatch($match);
		} catch (RoutingException $exception) {
			$callable = $this->fallback;
			$args = [];
		}
		try {
			call_user_func($this->before);
			call_user_func_array($callable, $args);
		} catch (Exception $exception) {
			call_user_func_array($this->error, [$exception]);
		}
	}

	public function parseMatch(array $match): array
	{
		$callable = $match["_callable"];
		$args = array_values(
			array_filter(
				$match,
				function ($value, $key) {
					return $key != "_callable";
				},
				ARRAY_FILTER_USE_BOTH
			)
		);
		return [
			$callable,
			$args
		];
	}
}