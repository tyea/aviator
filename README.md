# Aviator

## About

Aviator is a Symfony based microframework for building prototypes, microsites, and hobbyist projects.

## Installation

```
composer require tyea/aviator
```

## Requirements

* PHP >= 8.0

## Features

* Environment variables - `env()`
* Requests - `request()`
* Responses - `response()->raw()`, `response()->redirect()`, and `response()->json()`
* Debugging - `dd()`
* Templating - `template()->configure()`, `template()->global()`, and `template()->render()`
* Routing - `app()->route()`, `app()->fallback()`, and `app()->start()`
* Hooks - `app()->before()`
* Error handling - `app()->error()`
* Sessions - `session()`
* Validation - `validate()`
* DateTimes - `now()`
* MySQL - `mysql()->configure()`, `mysql()->execute()`, `mysql()->insert()`, `mysql()->rows()`, `mysql()->row()`, `mysql()->column()`, `mysql()->value()`, `mysql()->map()`, `mysql()->update()`, `mysql()->delete()`, `mysql()->migrate()`, `mysql()->table()->insert()`, `mysql()->table()->row()`, `mysql()->table()->update()`, `mysql()->table()->delete()`, `MYSQL_DATETIME`, `MYSQL_DATE`, `MYSQL_TIME`, `MYSQL_TRUE`, and `MYSQL_FALSE`
* Redis - `redis()->configure()` and `redis()->command()`
* Curl - `curl()`

## Examples

```
$data = request()->request->all();
$rules = [
    "email_address" => [
        "Type" => [
            "type" => "string",
        ],
        "Length" => [
            "min" => 1,
            "max" => 255
        ],
        "EmailAddress" => []
    ]
];
$errors = validate($data, $rules);
if ($errors) {
    response()->json(["errors" => $errors], 400);
}
```

```
$dsn = sprintf(
	"mysql:host=%s;port=%s;dbname=%s;charset=%s",
	env("MYSQL_HOST"),
	env("MYSQL_PORT"),
	env("MYSQL_DATABASE"),
	env("MYSQL_CHARACTER_SET")
);
$options = [
	Pdo::ATTR_ERRMODE => Pdo::ERRMODE_EXCEPTION,
	Pdo::ATTR_EMULATE_PREPARES => false,
    Pdo::ATTR_TIMEOUT => 3
];
mysql()->configure($dsn, env("MYSQL_USERNAME"), env("MYSQL_PASSWORD"), $options);
```

```
redis()->configure([
    "host" => env("REDIS_HOST"),
    "port" => env("REDIS_PORT"),
    "connectTimeout" => 3
]);
```

```
$response = curl()->request("GET", "http://www.example.com/foo");
if ($response->getStatusCode() == 200) {
    echo $response->getContent() . "\n";
}
```

## Snippets

```
$vars = parse_ini_file("/foo/bar/baz/.env", false, INI_SCANNER_TYPED);
$_ENV = array_merge($_ENV, $vars);
```

```
if (request()->getContentType() == "json" && request()->getContent() != "") {
	request()->request->replace(request()->toArray());
}
```

```
function handle_error(Throwable $throwable): void
{
	if (env("DEBUG")) {
		dd($throwable);
	}
	error_log($throwable);
	response()->json(["errors" => null], 500);
}
```

```
session(["cookie_httponly" => true])->start();
```

## Author

Written by Tom Yeadon in July 2021.