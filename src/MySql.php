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

	private function statement(string $query, array $params = []): PdoStatement
	{
		$statement = $this->pdo()->prepare($query);
		if (!$statement->execute($params)) {
			throw new Exception();
		}
		return $statement;
	}

	public function execute(string $query, array $params = []): int
	{
		$statement = $this->statement($query, $params);
		return $statement->rowCount();
	}

	public function insert(string $table, array $row): int
	{
		$columns = [];
		$params = [];
		foreach ($row as $column => $value) {
			$columns[] = $column;
			$params[] = $value;
		}
		if (!$columns) {
			throw new Exception();
		}
		$query = sprintf(
			"INSERT INTO `%s` (%s) VALUES (%s);",
			$table,
			"`" . implode("`, `", $columns) . "`",
			implode(", ", array_fill(0, count($columns), "?"))
		);
		$this->statement($query, $params);
		return $this->pdo()->lastInsertId();
	}

	public function rows(string $query, array $params = []): array
	{
		$statement = $this->statement($query, $params);
		return $statement->fetchAll(Pdo::FETCH_ASSOC);
	}

	public function row(string $query, array $params = []): ?array
	{
		$rows = $this->rows($query, $params);
		return $rows[0] ?? null;
	}

	public function column(string $query, array $params = []): array
	{
		$statement = $this->statement($query, $params);
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
		$statement = $this->statement($query, $params);
		$rows = $statement->fetchAll(Pdo::FETCH_NUM);
		$map = [];
		foreach ($rows as $row) {
			$map[$row[0]] = $row[1];
		}
		return $map;
	}

	public function update(string $table, array $row, string $primaryKey = "id"): void
	{
		$columns = [];
		$params = [];
		$id = null;
		foreach ($row as $column => $value) {
			if ($column == $primaryKey) {
				$id = $value;
				continue;
			}
			$columns[] = $column;
			$params[] = $value;
		}
		if (!$id || !$columns) {
			throw new Exception();
		}
		$query = sprintf(
			"UPDATE `%s` SET %s WHERE `%s` = ?;",
			$table,
			"`" . implode("` = ?, `", $columns) . "` = ?",
			$primaryKey
		);
		$params[] = $id;
		$this->statement($query, $params);
	}

	public function begin(): void
	{
		$this->pdo()->beginTransaction();
	}

	public function end(): void
	{
		$this->pdo()->commit();
	}
}