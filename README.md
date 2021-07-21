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
	App::request()->attributes->set("debug", "false");
});

App::route("GET", "/", function () {
	App::redirect("/home");
});

App::route("GET", "/api", function () {
	App::json((object) []);
});

App::fallback(function () {
	App::response("<h1>Not Found</h1>", 404);
});

App::error(function ($throwable) {
	App::dd($throwable);
});

App::start();
```

## Author

Written by Tom Yeadon in July 2021.