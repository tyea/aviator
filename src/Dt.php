<?php

namespace Tyea\Aviator;

use DateTime;
use DateTimeZone;

class Dt
{
	private function __construct()
	{
	}

	public const MYSQL_DATE = "Y-m-d";
	public const MYSQL_TIME = "H:i:s";
	public const MYSQL_DATETIME = "Y-m-d H:i:s";

	public static function now(): DateTime
	{
		return new DateTime("now", new DateTimeZone("UTC"));
	}
}