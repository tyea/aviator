<?php

namespace Tyea\Aviator;

use Symfony\Component\HttpFoundation\Response as RawResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class Response
{
	private $headers = [];

	public function headers(string $name, mixed $value): void
	{
		$this->headers[$name] = $value;
	}

	public function raw(string $content, int $code = 200, array $headers = []): void
	{
		$headers = array_merge($this->headers, $headers);
		$response = new RawResponse($content, $code, $headers);
		$response->prepare(request());
		$response->send();
		die();
	}

	public function redirect(string $destination, int $code = 302, array $headers = []): void
	{
		$headers = array_merge($this->headers, $headers);
		$response = new RedirectResponse($destination, $code, $headers);
		$response->prepare(request());
		$response->send();
		die();
	}

	function json(mixed $data, int $code = 200, array $headers = []): void
	{
		$headers = array_merge($this->headers, $headers);
		$response = new JsonResponse($data, $code, $headers);
		$response->prepare(request());
		$response->send();
		die();
	}
}