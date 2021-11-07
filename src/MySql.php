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

	public function execute(string $query, array $params = []): PdoStatement
	{
		$statement = $this->pdo()->prepare($query);
		$statement->execute($params);
		return $statement;
	}

	public function insert(string $table, array $row): ?int
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
			implode(", ", $columns),
			implode(", ", array_fill(0, count($columns), "?"))
		);
		$this->execute($query, $params);
		return $this->pdo()->lastInsertId() ?: null;
	}

	public function row(string $query, array $params = []): ?array
	{
		$rows = $this->rows($query, $params);
		return $rows[0] ?? null;
	}

	public function column(string $query, array $params = [])
	{
		$columns = $this->columns($query, $params);
		return $columns[0] ?? null;
	}

	public function rows(string $query, array $params = []): array
	{
		$statement = $this->execute($query, $params);
		return $statement->fetchAll(Pdo::FETCH_ASSOC);
	}

	public function columns(string $query, array $params = []): array
	{
		$statement = $this->execute($query, $params);
		$rows = $statement->fetchAll(Pdo::FETCH_NUM);
		$columns = [];
		foreach ($rows as $row) {
			$columns[] = $row[0];
		}
		return $columns;
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

	public function update(string $table, array $row): void
	{
		$columns = [];
		$params = [];
		$id = null;
		foreach ($row as $column => $value) {
			if ($column == "id") {
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
			"UPDATE `%s` SET %s WHERE `id` = ?;",
			$table,
			implode(" = ?, ", $columns) . " = ?"
		);
		$params[] = $id;
		$this->execute($query, $params);
	}

	public function delete(string $table, array $row): void
	{
		$id = null;
		foreach ($row as $column => $value) {
			if ($column == "id") {
				$id = $value;
				break;
			}
		}
		if (!$id) {
			throw new Exception();
		}
		$query = sprintf(
			"DELETE FROM `%s` WHERE `id` = ?;",
			$table
		);
		$this->execute($query, [$id]);
	}

	public function modify(string $query, array $params = []): int
	{
		$statement = $this->execute($query, $params);
		return $statement->rowCount();
	}
}