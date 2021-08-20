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

class App
{
	private function __construct()
	{
	}

	protected static $before;

	public static function before(callable $callback = null)
	{
		if (!$callback) {
			if (!self::$before) {
				self::$before = function () {
				};
			}
			return self::$before;
		}
		self::$before = $callback;
	}

	protected static $routes;

	private static function routes(): Routes
	{
		if (!self::$routes) {
			self::$routes = new Routes();
		}
		return self::$routes;
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
		self::routes()->add($name, $route);
	}

	protected static $fallback;

	public static function fallback(callable $callback = null)
	{
		if (!$callback) {
			if (!self::$fallback) {
				self::$fallback = function () {
					self::json((object) [], 404);
				};
			}
			return self::$fallback;
		}
		self::$fallback = $callback;
	}

	protected static $error;

	public static function error(callable $callback = null)
	{
		if (!$callback) {
			if (!self::$error) {
				self::$error = function (Throwable $throwable) {
					error_log($throwable);
					self::json((object) [], 500);
				};
			}
			return self::$error;
		}
		self::$error = $callback;
	}

	public static function start(): void
	{
		$context = new Context();
		$context->fromRequest(self::request());
		$matcher = new Matcher(self::routes(), $context);
		try {
			$match = $matcher->match(self::request()->getPathInfo());
			$callback = $match["_callback"];
			$args = array_filter(
				$match,
				function ($value, $key) {
					return $key != "_callback";
				},
				ARRAY_FILTER_USE_BOTH
			);
		} catch (RoutingException $exception) {
			$callback = self::fallback();
			$args = [];
		}
		try {
			call_user_func(self::before());
			call_user_func_array($callback, $args);
		} catch (Throwable $throwable) {
			call_user_func_array(self::error(), [$throwable]);
		}
	}

	protected static $request;

	public static function request(): Request
	{
		if (!self::$request) {
			self::$request = Request::createFromGlobals();
		}
		return self::$request;
	}

    protected static $session;

    public static function session(): Session
    {
        if (!self::$session) {
            self::$session = new Session();
        }
        return self::$session;
    }

	public static function response(string $content, int $code = 200, array $headers = []): void
	{
		$response = new Response($content, $code, $headers);
		$response->prepare(self::request());
		$response->send();
		exit();
	}

	public static function redirect(string $destination, int $code = 302, array $headers = []): void
	{
		$response = new Redirect($destination, $code, $headers);
		$response->prepare(self::request());
		$response->send();
		exit();
	}

	public static function json($data, int $code = 200, array $headers = []): void
	{
		$response = new JsonResponse($data, $code, $headers);
		$response->prepare(self::request());
		$response->send();
		exit();
	}

	public static function dd($var = null): void
	{
		ob_start();
		var_dump($var);
		$dump = ob_get_clean();
		$html = "<pre>" . htmlentities($dump) . "</pre>";
		self::response($html, 500);
	}
}