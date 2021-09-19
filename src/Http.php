<?php

namespace Tyea\Aviator;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse as Redirect;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class Http
{
	private function __construct()
	{
	}

	private static $request;

	public static function request(): Request
	{
		if (!Http::$request) {
			Http::$request = Request::createFromGlobals();
		}
		return Http::$request;
	}

	private static $session;

	public static function session(): Session
	{
		if (!Http::$session) {
			Http::$session = new Session();
		}
		return Http::$session;
	}

	public static function response(string $content, int $code = 200, array $headers = []): void
	{
		$response = new Response($content, $code, $headers);
		$response->prepare(Http::request());
		$response->send();
		die();
	}

	public static function redirect(string $destination, int $code = 302, array $headers = []): void
	{
		$response = new Redirect($destination, $code, $headers);
		$response->prepare(Http::request());
		$response->send();
		die();
	}

	public static function json($data, int $code = 200, array $headers = []): void
	{
		$response = new JsonResponse($data, $code, $headers);
		$response->prepare(Http::request());
		$response->send();
		die();
	}
}