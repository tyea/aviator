<?php

namespace Tyea\Aviator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouteCollection as Routes;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RequestContext as Context;
use Symfony\Component\Routing\Matcher\UrlMatcher as Matcher;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse as Redirect;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Exception;

class App
{
	private function __construct()
	{
	}

	private static $request;

	public static function request(): Request
	{
		if (!App::$request) {
			App::$request = Request::createFromGlobals();
		}
		return App::$request;
	}

	private static $session;

	public static function session(): Session
	{
		if (!App::$session) {
			App::$session = new Session();
		}
		return App::$session;
	}

	public static function response(string $content, int $code = 200, array $headers = []): void
	{
		$response = new Response($content, $code, $headers);
		$response->prepare(App::request());
		$response->send();
		exit();
	}

	public static function redirect(string $destination, int $code = 302, array $headers = []): void
	{
		$response = new Redirect($destination, $code, $headers);
		$response->prepare(App::request());
		$response->send();
		exit();
	}

	public static function json($data, int $code = 200, array $headers = []): void
	{
		$response = new JsonResponse($data, $code, $headers);
		$response->prepare(App::request());
		$response->send();
		exit();
	}

	private static $before;

	public static function before(callable $before = null)
	{
		if (!$before) {
			if (!App::$before) {
				App::$before = function () {
				};
			}
			return App::$before;
		}
		App::$before = $before;
	}

	private static $routes;

	private static function routes(): Routes
	{
		if (!App::$routes) {
			App::$routes = new Routes();
		}
		return App::$routes;
	}

	public static function route($methods, string $path, callable $callback): void
	{
		if (!is_array($methods)) {
			$methods = [$methods];
		}
		$name = implode(",", $methods) . "_" . $path;
		$route = new Route($path);
		$route->setMethods($methods);
		$route->addDefaults(["_callback" => $callback]);
		App::routes()->add($name, $route);
	}

	private static $fallback;

	public static function fallback(callable $fallback = null)
	{
		if (!$fallback) {
			if (!App::$fallback) {
				App::$fallback = function () {
					App::json((object) [], 404);
				};
			}
			return App::$fallback;
		}
		App::$fallback = $fallback;
	}

	private static $error;

	public static function error(callable $error = null)
	{
		if (!$error) {
			if (!App::$error) {
				App::$error = function (Exception $exception) {
				    if (env("DATABASE_USERNAME", "false") == "true") {
				        dd($exception);
                    }
					error_log($exception);
					App::json((object) [], 500);
				};
			}
			return App::$error;
		}
		App::$error = $error;
	}

	public static function start(): void
	{
		$context = new Context();
		$context->fromRequest(App::request());
		$matcher = new Matcher(App::routes(), $context);
		try {
			$match = $matcher->match(App::request()->getPathInfo());
			$callback = $match["_callback"];
			$args = array_filter(
				$match,
				function ($value, $key) {
					return $key != "_callback";
				},
				ARRAY_FILTER_USE_BOTH
			);
		} catch (RoutingException $exception) {
			$callback = App::fallback();
			$args = [];
		}
		try {
			call_user_func(App::before());
			call_user_func_array($callback, $args);
		} catch (Exception $exception) {
			call_user_func_array(App::error(), [$exception]);
		}
	}
}