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
	private function __construct()
	{
	}

	private static $before;

	public static function before(callable $before)
	{
		App::$before = $before;
	}

	private static $fallback;

	public static function fallback(callable $fallback)
	{
		App::$fallback = $fallback;
	}

	private static $error;

	public static function error(callable $error)
	{
		App::$error = $error;
	}

	private static $routes;

	private static function routes(): Routes
	{
		if (!App::$routes) {
			App::$routes = new Routes();
		}
		return App::$routes;
	}

	public static function route(string $method, string $path, callable $callback): void
	{
		$route = new Route($path);
		$route->setMethods([$method]);
		$route->addDefaults(["_callback" => $callback]);
		$name = $method . "_" . $path;
		App::routes()->add($name, $route);
	}

	public static function run(): void
	{
		if (!App::$before || !App::$fallback || !App::$error) {
			throw new Exception();
		}
		$context = new Context();
		$context->fromRequest(Http::request());
		$matcher = new Matcher(App::routes(), $context);
		try {
			$match = $matcher->match(Http::request()->getPathInfo());
			$callback = $match["_callback"];
			$filter = function ($value, $key) {
				return $key != "_callback";
			};
			$args = array_filter($match, $filter, ARRAY_FILTER_USE_BOTH);
		} catch (RoutingException $exception) {
			$callback = App::$fallback;
			$args = [];
		}
		try {
			call_user_func(App::$before);
			call_user_func_array($callback, $args);
		} catch (Exception $exception) {
			call_user_func_array(App::$error, [$exception]);
		}
	}

	public static function dd($var = null): void
	{
		ob_start();
		var_dump($var);
		$dump = ob_get_clean();
		$content = "<pre>" . htmlspecialchars($dump, ENT_COMPAT, "UTF-8") . "</pre>";
		Http::response($content, 500);
	}
}