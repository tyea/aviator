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
	private $routes;
	private $fallback;
	private $error;

	public function __construct()
	{
		$this->before = function () {
		};
		$this->routes = new Routes();
		$this->fallback = function () {
			response()->raw("Not Found", 404, ["Content-Type" => "text/plain"]);
		};
		$this->error = function () {
			response()->raw("Internal Server Error", 500, ["Content-Type" => "text/plain"]);
		};
	}

	function before(callable $callable): void
	{
		$this->before = $callable;
	}

	function route(string|array $methods, string $path, callable $callable): void
	{
		if (!is_array($methods)) {
			$methods = [$methods];
		}
		$name = implode(",", $methods) . "_" . $path;
		$route = new Route($path);
		$route->setMethods($methods);
		$route->addDefaults(["_callable" => $callable]);
		$this->routes->add($name, $route);
	}

	function fallback(callable $callable): void
	{
		$this->fallback = $callable;
	}

	function error(callable $callable): void
	{
		$this->error = $callable;
	}

	function start(): void
	{
		$context = new Context();
		$context->fromRequest(request());
		$matcher = new Matcher($this->routes, $context);
		try {
			$match = $matcher->match(request()->getPathInfo());
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
}