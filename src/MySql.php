<?php

namespace Tyea\Aviator;

use Pdo;
use PdoStatement;
use Exception;

class MySql
{
	private function __construct()
	{
	}

	private static $dsn;
	private static $username;
	private static $password;
	private static $options;

	public static function configure(string $dsn, string $username, string $password = null, array $options = []): void
	{
		MySql::$dsn = $dsn;
		MySql::$username = $username;
		MySql::$password = $password;
		MySql::$options = $options;
	}

	private static $pdo;

	private static function pdo(): Pdo
	{
		if (!MySql::$pdo) {
			if (!MySql::$dsn || !MySql::$username) {
				throw new Exception();
			}
			MySql::$pdo = new Pdo(MySql::$dsn, MySql::$username, MySql::$password, MySql::$options);
		}
		return MySql::$pdo;
	}

	public static function execute(string $query, array $params = []): PdoStatement
	{
		$statement = MySql::pdo()->prepare($query);
		$statement->execute($params);
		return $statement;
	}

	public static function insert(string $table, array $row): ?int
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
		MySql::execute($query, $params);
		return MySql::pdo()->lastInsertId() ?: null;
	}

	public static function row(string $query, array $params = []): ?array
	{
		$rows = MySql::rows($query, $params);
		return $rows[0] ?? null;
	}

	public static function column(string $query, array $params = [])
	{
		$columns = MySql::columns($query, $params);
		return $columns[0] ?? null;
	}

	public static function rows(string $query, array $params = []): array
	{
		$statement = MySql::execute($query, $params);
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function columns(string $query, array $params = []): array
	{
		$statement = MySql::execute($query, $params);
		$rows = $statement->fetchAll(PDO::FETCH_NUM);
		$columns = [];
		foreach ($rows as $row) {
			$columns[] = $row[0];
		}
		return $columns;
	}

	public static function map(string $query, array $params = []): array
	{
		$statement = MySql::execute($query, $params);
		$rows = $statement->fetchAll(PDO::FETCH_NUM);
		$map = [];
		foreach ($rows as $row) {
			$map[$row[0]] = $row[1];
		}
		return $map;
	}

	public static function update(string $table, array $row): void
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
		MySql::execute($query, $params);
	}

	public static function delete(string $table, array $row): void
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
		MySql::execute($query, [$id]);
	}

	public static function modify(string $query, array $params = []): int
	{
		$statement = MySql::execute($query, $params);
		return $statement->rowCount();
	}
}