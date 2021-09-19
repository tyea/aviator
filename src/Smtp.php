<?php

namespace Tyea\Aviator;

use Swift_SmtpTransport as SmtpTransport;
use Swift_Message as Message;
use Swift_Mailer as Mailer;
use Exception;

class Smtp
{
	private function __construct()
	{
	}

	private static $host;
	private static $port;
	private static $username;
	private static $password;
	private static $encryption;

	public static function configure(string $host, string $port, string $username, string $password = null, string $encryption = null): void
	{
		Smtp::$host = $host;
		Smtp::$port = $port;
		Smtp::$username = $username;
		Smtp::$password = $password;
		Smtp::$encryption = $encryption;
	}

	private static $mailer;

	private static function mailer(): Mailer
	{
		if (!Smtp::$mailer) {
			if (!Smtp::$host || !Smtp::$port || !Smtp::$username) {
				throw new Exception();
			}
			$transport = new SmtpTransport(Smtp::$host, Smtp::$host, Smtp::$encryption);
			$transport->setUsername(Smtp::$username);
			$transport->setPassword(Smtp::$password);
			Smtp::$mailer = new Mailer($transport);
		}
		return Smtp::$mailer;
	}

	public static function send(string $from, string $to, string $subject, string $body): void
	{
		$message = new Message($subject);
		$message->setFrom([$from]);
		$message->setTo([$to]);
		$message->setBody($body);
		if (!Smtp::mailer()->send($message)) {
			throw new Exception();
		}
	}
}