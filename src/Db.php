<?php

namespace Tyea\Aviator;

use PDO as Pdo;
use PDOStatement as PdoStatement;
use Exception;

class Db
{
	private function __construct()
	{
	}

	private static $pdo;

	public static function pdo(): ?Pdo
	{
		if (!Db::$pdo) {
			$dsn = sprintf(
				"mysql:host=%s;port=%s;dbname=%s;charset=%s",
				env("DATABASE_HOST", "127.0.0.1"),
				env("DATABASE_PORT", "3306"),
				env("DATABASE_NAME", "production"),
				env("DATABASE_CHARSET", "utf8mb4")
			);
			$username = env("DATABASE_USERNAME", "root");
			$password = env("DATABASE_PASSWORD", "password");
			$options = [
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::ATTR_EMULATE_PREPARES => false
			];
			Db::$pdo = new Pdo($dsn, $username, $password, $options);
		}
		return Db::$pdo;
	}

	public static function execute(string $query, array $params = []): PdoStatement
	{
		$statement = Db::pdo()->prepare($query);
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
		Db::execute($query, $params);
		return Db::pdo()->lastInsertId() ?: null;
	}

	public static function row(string $query, array $params = []): ?array
	{
		$rows = Db::rows($query, $params);
		return $rows[0] ?? null;
	}

	public static function column(string $query, array $params = [])
	{
		$columns = Db::columns($query, $params);
		return $columns[0] ?? null;
	}

	public static function rows(string $query, array $params = []): array
	{
		$statement = Db::execute($query, $params);
		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function columns(string $query, array $params = []): array
	{
		$statement = Db::execute($query, $params);
		$rows = $statement->fetchAll(PDO::FETCH_NUM);
		$columns = [];
		foreach ($rows as $row) {
			$columns[] = $row[0];
		}
		return $columns;
	}

	public static function map(string $query, array $params = []): array
	{
		$statement = Db::execute($query, $params);
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
		Db::execute($query, $params);
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
		Db::execute($query, [$id]);
	}

	public static function modify(string $query, array $params = []): int
	{
		$statement = Db::execute($query, $params);
		return $statement->rowCount();
	}
}