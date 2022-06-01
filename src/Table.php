<?php

namespace Tyea\Aviator;

use Exception;

class Table
{
	private $name;
	private $primaryKey;

	public function __construct(string $name, string $primaryKey)
	{
		$this->name = $name;
		$this->primaryKey = $primaryKey;
	}

	public function insert(array $row): array
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
			$this->name,
			"`" . implode("`, `", $columns) . "`",
			implode(", ", array_fill(0, count($columns), "?"))
		);
		$id = mysql()->insert($query, $params);
		return $this->row($id);
	}

	public function row(mixed $id): ?array
	{
		$query = sprintf(
			"SELECT * FROM `%s` WHERE `%s` = ? LIMIT 1;",
			$this->name,
			$this->primaryKey
		);
		return mysql()->row($query, [$id]);
	}

	public function update(array $row): void
	{
		$columns = [];
		$params = [];
		$id = null;
		foreach ($row as $column => $value) {
			if ($column == $this->primaryKey) {
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
			"UPDATE `%s` SET %s WHERE `%s` = ? LIMIT 1;",
			$this->name,
			"`" . implode("` = ?, `", $columns) . "` = ?",
			$this->primaryKey
		);
		$params[] = $id;
		mysql()->update($query, $params);
	}

	public function delete(array $row): void
	{
		$id = null;
		foreach ($row as $column => $value) {
			if ($column == $this->primaryKey) {
				$id = $value;
				break;
			}
		}
		if (!$id) {
			throw new Exception();
		}
		$query = sprintf(
			"DELETE FROM `%s` WHERE `%s` = ? LIMIT 1;",
			$this->name,
			$this->primaryKey
		);
		mysql()->delete($query, [$id]);
	}
}