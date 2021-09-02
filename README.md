# Aviator

## About

Aviator is a Symfony based microframework for building prototypes, microsites, and hobbyist projects.

## Installation

```
composer require tyea/aviator
```

## Features

* Config - `env()`
* Requests - `App::request()`
* Responses - `App::response()`, `App::redirect()`, and `App::json()`
* Routing - `App::route()`, `App::fallback()`, and `App::start()`
* Hooks - `App::before()`
* Error Handling - `App::error()`
* Sessions - `App::session()`
* Templating - `render()`
* Debugging - `dd()`

## Example

```
<?php

define("__APP__", __DIR__ . "/..");
require(__APP__ . "/vendor/autoload.php");

use Tyea\Aviator\App;

App::before(function () {
	$_ENV["DEBUG"] = true;
	$_ENV["TEMPLATES_DIRECTORY"] = __APP__ . "/src/Templates";
	if (App::request()->headers->get("Content-Type") == "application/json") {
		App::request()->request->replace(App::request()->toArray());
	}
	App::session()->start();
});

App::route("GET", "/", function () {
	$redirect = App::request()->query->get("redirect");
	if ($redirect) {
		App::redirect($redirect);
	}
	App::response(render("home.twig"));
});

App::route("GET", "/me", function () {
	$me = App::session()->get("user");
	if (!$me) {
		App::json((object) [], 404);
	}
	App::json($me);
});

App::fallback(function () {
	App::response(render("fallback.twig"), 404);
});

App::error(function (Exception $exception) {
	if (env("DEBUG", false)) {
		dd($exception);
	}
	error_log($exception);
	App::response(render("error.twig"), 500)
});

App::start();
```

## Gotchas

* Aviator responses call `die()` as soon as they have been sent
* Aviator relies upon certain `$_ENV` variables to be set in order to use `render()`

## Author

Written by Tom Yeadon in July 2021.