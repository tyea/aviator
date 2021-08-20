# Aviator

## About

Aviator is a Symfony based microframework for prototyping applications.

## Requirements

* PHP >= 7.2

## Installation

```
composer require tyea/aviator
```

## Example

```
<?php

require(__DIR__ . "/../vendor/autoload.php");

use Tyea\Aviator\App;

App::before(function () {
	App::session()->start();
});

App::route("GET", "/", function () {
	App::redirect("/foo");
});

App::route("GET", "/foo", function () {
	App::json((object) []);
});

App::fallback(function () {
	App::response("<h1>Bar</h1>", 404);
});

App::error(function ($throwable) {
	App::dd($throwable);
});

App::start();
```

## Templating

```
composer require twig/twig
```

```
<?php

use Tyea\Aviator\App as BaseApp;
use Twig\Loader\FilesystemLoader as Loader;
use Twig\Environment as Engine;

class App extends BaseApp
{
	public static function view(string $template, array $data = [], int $code = 200, array $headers = []): void
	{
		$loader = new Loader(__APP__ . "/src/Views");
		$environment = new Engine($loader);
		$content = $environment->render($template, $data);
		self::response($content, $code, $headers);
	}
}
```

```
<?php

require(__DIR__ . "/../vendor/autoload.php");

App::route("GET", "/baz", function () {
	App::view("baz.twig", ["qux" => "QUX"]);
});

App::start();
```

## Author

Written by Tom Yeadon in July 2021.