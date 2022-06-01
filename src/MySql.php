<?php

namespace Tyea\Aviator;

use Pdo;
use PdoStatement;
use Exception;

class MySql
{
	private $dsn;
	private $username;
	private $password;
	private $options;

	public function configure(string $dsn, string $username, string $password, array $options = []): void
	{
		$this->dsn = $dsn;
		$this->username = $username;
		$this->password = $password;
		$this->options = $options;
	}

	private $pdo;

	public function pdo(): Pdo
	{
		if (!$this->pdo) {
			$this->pdo = new Pdo($this->dsn, $this->username, $this->password, $this->options);
		}
		return $this->pdo;
	}

	private function execute(string $query, array $params = []): PdoStatement
	{
		$statement = $this->pdo()->prepare($query);
		if (!$statement->execute($params)) {
			throw new Exception();
		}
		return $statement;
	}

	public function insert(string $query, array $params = []): mixed
	{
		$this->execute($query, $params);
		return $this->pdo()->lastInsertId();
	}

	public function rows(string $query, array $params = []): array
	{
		$statement = $this->execute($query, $params);
		return $statement->fetchAll(Pdo::FETCH_ASSOC);
	}

	public function row(string $query, array $params = []): ?array
	{
		$rows = $this->rows($query, $params);
		return $rows[0] ?? null;
	}

	public function column(string $query, array $params = []): array
	{
		$statement = $this->execute($query, $params);
		$rows = $statement->fetchAll(Pdo::FETCH_NUM);
		$column = [];
		foreach ($rows as $row) {
			$column[] = $row[0];
		}
		return $column;
	}

	public function value(string $query, array $params = []): mixed
	{
		$column = $this->column($query, $params);
		return $column[0] ?? null;
	}

	public function map(string $query, array $params = []): array
	{
		$statement = $this->execute($query, $params);
		$rows = $statement->fetchAll(Pdo::FETCH_NUM);
		$map = [];
		foreach ($rows as $row) {
			$map[$row[0]] = $row[1];
		}
		return $map;
	}

	public function update(string $query, array $params = []): int
	{
		$statement = $this->execute($query, $params);
		return $statement->rowCount();
	}

	public function delete(string $query, array $params = []): int
	{
		$statement = $this->execute($query, $params);
		return $statement->rowCount();
	}

	public function migrate(string $migrations): void
	{
		$tables = mysql()->column("SHOW TABLES;");
		if (!in_array("migrations", $tables)) {
			mysql()->execute("
			CREATE TABLE `migrations` (
				`id` BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,
				`name` VARCHAR(255) NOT NULL,
				`created_at` DATETIME NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE (`name`)
			);
		");
		}
		$files = glob($migrations);
		sort($files);
		foreach ($files as $file) {
			$name = basename($file);
			$count = mysql()->value("SELECT COUNT(`id`) FROM `migrations` WHERE `name` = ?;", [$name]);
			if (!$count) {
				mysql()->execute(file_get_contents($file));
				mysql()->table("migrations")->insert([
					"name" => $name,
					"created_at" => now()->format(MYSQL_DATETIME)
				]);
			}
		}
	}

	public function table(string $name, string $primaryKey = "id"): Table
	{
		return new Table($name, $primaryKey);
	}
}