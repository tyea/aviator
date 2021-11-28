<?php

namespace Tyea\Aviator;

use Twig\Loader\FilesystemLoader as Loader;
use Twig\Environment as Engine;

class Template
{
	private $path;
	private $options;

	public function configure(string $path, array $options = []): void
	{
		$this->path = $path;
		$this->options = $options;
	}

	private $engine;

	public function engine(): Engine
	{
		if (!$this->engine) {
			$this->engine = new Engine(new Loader($this->path), $this->options);
		}
		return $this->engine;
	}

	private $data = [];

	public function data(string $key, mixed $value): void
	{
		$this->data[$key] = $value;
	}

	public function render(string $template, array $data = []): string
	{
		$data = array_merge($this->data, $data);
		return $this->engine()->render($template, $data);
	}
}