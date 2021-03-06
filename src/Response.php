<?php

namespace Tyea\Aviator;

use Symfony\Component\HttpFoundation\Response as RawResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class Response
{
	public function raw(string $content, int $code = 200, array $headers = []): void
	{
		$response = new RawResponse($content, $code, $headers);
		$response->prepare(request());
		$response->send();
		die();
	}

	public function redirect(string $url, int $code = 302, array $headers = []): void
	{
		$response = new RedirectResponse($url, $code, $headers);
		$response->prepare(request());
		$response->send();
		die();
	}

	function json(mixed $data, int $code = 200, array $headers = []): void
	{
		$response = new JsonResponse($data, $code, $headers);
		$response->prepare(request());
		$response->send();
		die();
	}
}