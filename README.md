# Aviator

## About

Aviator is a Symfony based microframework for building prototypes, microsites, and hobbyist projects.

## Installation

```
composer require tyea/aviator
```

## Features

* Requests - `Http::request()`
* Responses - `Http::response()`, `Http::redirect()`, and `Http::json()`
* Sessions - `Http::session()`
* Routing - `App::route()`, `App::fallback()`, and `App::run()`
* Hooks - `App::before()`
* Error Handling - `App::error()`
* Debugging - `App::dd()`
* Templating - `Tpl::configure()` and `Tpl::render()`
* Environment Variables - `Env::configure()` and `Env::get()`
* Database - `Db::configure()`, `Db::execute()`, `Db::insert()`, `Db::row()`, `Db::rows()`, `Db::column()`, `Db::columns()`, `Db::map()`, `Db::update()`, `Db::delete()`, and `Db::modify()`
* DateTimes - `Dt::now()`, `Dt::DB_DATE`, `Dt::DB_TIME`, and `Dt::DB_DATETIME`
* SMTP - `Smtp::configure()` and `Smtp::send()`

## Examples

### Requests, Responses, Sessions, Hooks, Routing, Error Handling

```
<?php

require(__DIR__ . "/../vendor/autoload.php");

use Tyea\Aviator\App;
use Tyea\Aviator\Http;

App::before(function () {
	if (Http::request()->headers->get("Content-Type") == "application/json") {
		Http::request()->request->replace(Http::request()->toArray());
	}
	Http::session()->start();
});
App::route("GET", "/", function () {
	Http::json(["foo" => "FOO"])
});
App::fallback(function () {
	Http::json((object) [], 404)
});
App::error(function (Exception $exception) {
	error_log($exception);
	Http::json((object) [], 500)
});
App::run();
```

### Debugging

```
<?php

require(__DIR__ . "/../vendor/autoload.php");

use Tyea\Aviator\App;
use Tyea\Aviator\Http;

App::dd(Http::request());
```

### Templating

```
<?php

require(__DIR__ . "/../vendor/autoload.php");

use Tyea\Aviator\Tpl;
use Tyea\Aviator\Http;

Tpl::configure(__DIR__ . "/../src/Templates");
$content = Tpl::render("foo.twig", ["bar" => "BAR"]);
Http::response($content);
```

### Environment Variables, Database, and DateTimes

```
<?php

require(__DIR__ . "/../vendor/autoload.php");

use Tyea\Aviator\Env;
use Tyea\Aviator\Db;
use Tyea\Aviator\Dt;

Env::configure(__DIR__ . "/../.env");
$dsn = sprintf(
	"mysql:host=%s;port=%s;dbname=%s;charset=%s",
	Env::get("DB_HOST"),
	Env::get("DB_PORT"),
	Env::get("DB_DATABASE"),
	Env::get("DB_CHARACTER_SET")
);
$options = [
	Pdo::ATTR_ERRMODE => Pdo::ERRMODE_EXCEPTION,
	Pdo::ATTR_EMULATE_PREPARES => false
];
Db::configure($dsn, Env::get("DB_USERNAME"), Env::get("DB_PASSWORD"), $options);
$row = [
	"bar" => "FOO",
	"baz" => Dt::now()->format(Dt::DB_DATETIME)
];
Db::insert("foos", $row);
```

### SMTP

```
<?php

require(__DIR__ . "/../vendor/autoload.php");

use Tyea\Aviator\Env;
use Tyea\Aviator\Tpl;
use Tyea\Aviator\Smtp;

Env::configure(__DIR__ . "/../.env");
Tpl::configure(__DIR__ . "/../src/Templates");
Smtp::configure(
	Env::get("SMTP_HOST"),
	Env::get("SMTP_PORT"),
	Env::get("SMTP_USERNAME"),
	Env::get("SMTP_PASSWORD"),
	Env::get("SMTP_ENCRYPTION")
);
$content = Tpl::render("foo.twig", ["bar" => "BAR"]);
Smtp::send(Env::get("SMTP_FROM"), "qux@example.com", "Norf", $content);
```

## Todo

* Validation

## Author

Written by Tom Yeadon in July 2021.