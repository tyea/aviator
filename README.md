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
* Sessions - `session()`
* Validation - `validate()`
* Debugging - `dd()`
* MySQL - `mysql()->configure()`, `mysql()->create()`, `mysql()->rows()`, `mysql()->row()`, `mysql()->column()`, `mysql()->value()`, `mysql()->map()`, `mysql()->find()`, `mysql()->modify()`, `mysql()->update()`, `mysql()->delete()`
* DateTimes - `now()`, `MYSQL_DATETIME`, `MYSQL_DATE`, and `MYSQL_TIME`
* SMTP - `smtp()->configure()` and `smtp()->send()`
* Templating - `template()->configure()` and `template()->render()`
* Curl - `curl()`
* Redis - `redis()->configure()` and `redis()->command()`

## Author

Written by Tom Yeadon in July 2021.