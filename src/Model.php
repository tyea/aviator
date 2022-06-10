<?php

namespace Tyea\Aviator;

use Exception;

class Model
{
	protected $name;
	protected $primaryKey;

	public function __construct()
	{
		if (!$this->name || !$this->primaryKey) {
			throw new Exception();
		}
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
		$primaryKey = mysql()->insert($query, $params);
		return $this->row($primaryKey);
	}

	public function row(mixed $primaryKey): ?array
	{
		$query = sprintf(
			"SELECT * FROM `%s` WHERE `%s` = ? LIMIT 1;",
			$this->name,
			$this->primaryKey
		);
		return mysql()->row($query, [$primaryKey]);
	}

	public function update(array $row): void
	{
		$columns = [];
		$params = [];
		$primaryKey = null;
		foreach ($row as $column => $value) {
			if ($column == $this->primaryKey) {
				$primaryKey = $value;
				continue;
			}
			$columns[] = $column;
			$params[] = $value;
		}
		if (!$primaryKey || !$columns) {
			throw new Exception();
		}
		$query = sprintf(
			"UPDATE `%s` SET %s WHERE `%s` = ? LIMIT 1;",
			$this->name,
			"`" . implode("` = ?, `", $columns) . "` = ?",
			$this->primaryKey
		);
		$params[] = $primaryKey;
		mysql()->update($query, $params);
	}

	public function delete(array $row): void
	{
		$primaryKey = null;
		foreach ($row as $column => $value) {
			if ($column == $this->primaryKey) {
				$primaryKey = $value;
				break;
			}
		}
		if (!$primaryKey) {
			throw new Exception();
		}
		$query = sprintf(
			"DELETE FROM `%s` WHERE `%s` = ? LIMIT 1;",
			$this->name,
			$this->primaryKey
		);
		mysql()->delete($query, [$primaryKey]);
	}
}