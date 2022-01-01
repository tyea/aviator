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
* Templating - `template()->configure()` and `template()->render()`
* Routing - `app()->route()`, `app()->fallback()`, and `app()->start()`
* Hooks - `app()->before()`
* Error handling - `app()->error()`
* Sessions - `session()`
* Validation - `validate()`
* DateTimes - `now()`
* MySQL - `mysql()->configure()`, `mysql()->insert()`, `mysql()->create()`, `mysql()->rows()`, `mysql()->row
()`, `mysql()->column()`, `mysql()->value()`, `mysql()->map()`, `mysql()->find()`, `mysql()->modify()`, `mysql
()->update()`, `mysql()->delete()`, `MYSQL_DATETIME`, `MYSQL_DATE`, `MYSQL_TIME`, `MYSQL_TRUE`, and `MYSQL_FALSE`
* Redis - `redis()->configure()` and `redis()->command()`
* SMTP - `smtp()->configure()` and `smtp()->send()`
* Curl - `curl()`

## Author

Written by Tom Yeadon in July 2021.