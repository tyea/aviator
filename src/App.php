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
		$this->routes = new Routes();
	}

	function before(callable $callable): App
	{
		$this->before = $callable;
		return $this;
	}

	function route(string|array $methods, string $path, callable $callable): App
	{
		if (!is_array($methods)) {
			$methods = [$methods];
		}
		$name = implode(",", $methods) . "_" . $path;
		$route = new Route($path);
		$route->setMethods($methods);
		$route->addDefaults(["_callable" => $callable]);
		$this->routes->add($name, $route);
		return $this;
	}

	function fallback(callable $callable): App
	{
		$this->fallback = $callable;
		return $this;
	}

	function error(callable $callable): App
	{
		$this->error = $callable;
		return $this;
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