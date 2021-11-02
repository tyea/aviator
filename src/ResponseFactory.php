<?php

namespace Tyea\Aviator;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ResponseFactory
{
	public function raw(string $content, int $code = 200, array $headers = []): void
	{
		$response = new Response($content, $code, $headers);
		$response->prepare(Http::request());
		$response->send();
		die();
	}

	public function redirect(string $destination, int $code = 302, array $headers = []): void
	{
		$response = new RedirectResponse($destination, $code, $headers);
		$response->prepare(Http::request());
		$response->send();
		die();
	}

	function json($data, int $code = 200, array $headers = []): void
	{
		$response = new JsonResponse($data, $code, $headers);
		$response->prepare(Http::request());
		$response->send();
		die();
	}
}