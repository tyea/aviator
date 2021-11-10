<?php

namespace Tyea\Aviator;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Sequentially;

class CollectionFactory
{
	private function __construct()
	{
	}

	public static function createFromArray(array $constraints, bool $allowMissingFields, bool $allowExtraFields): Collection
	{
		$fields = [];
		foreach ($constraints as $field => $fieldConstraints) {
			$constraints = [];
			foreach ($fieldConstraints as $constraint => $options) {
				$constraints[] = new $constraint($options);
			}
			$fields[$field] = new Sequentially(["constraints" => $constraints]);
		}
		$options = [
			"fields" => $fields,
			"allowMissingFields" => $allowMissingFields,
			"allowExtraFields" => $allowExtraFields
		];
		return new Collection($options);
	}
}