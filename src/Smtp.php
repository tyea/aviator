<?php

namespace Tyea\Aviator;

use Swift_SmtpTransport as SmtpTransport;
use Swift_Mailer as Mailer;
use Swift_Message as Message;
use Exception;

class Smtp
{
	private $host;
	private $username;
	private $password;
	private $port;
	private $encryption;

	public function configure(string $host, string $username, string $password, int $port = 587, string $encryption = "tls"): void
	{
		$this->host = $host;
		$this->username = $username;
		$this->password = $password;
		$this->port = $port;
		$this->encryption = $encryption;
	}

	private $mailer;

	public function mailer(): Mailer
	{
		if (!$this->mailer) {
			$transport = new SmtpTransport($this->host, $this->port, $this->encryption);
			$transport->setUsername($this->username);
			$transport->setPassword($this->password);
			$this->mailer = new Mailer($transport);
		}
		return $this->mailer;
	}

	public function send(string $from, string $to, string $subject, string $body): void
	{
		$message = new Message($subject);
		$message->setFrom($from);
		$message->setTo($to);
		$message->setBody($body);
		if (!$this->mailer()->send($message)) {
			throw new Exception();
		}
	}
}