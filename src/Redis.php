<?php

namespace Tyea\Aviator;

use Redis as Client;

class Redis
{
	private $options;

	public function configure(array $options): void
	{
		$this->options = $options;
	}

	private $client;

	public function client(): Client
	{
		if (!$this->client) {
			$this->client = new Client($this->options);
		}
		return $this->client;
	}

	public function command(string $command, ...$arguments): mixed
	{
		$callback = [$this->client(), $command];
		return call_user_func_array($callback, $arguments);
	}
}