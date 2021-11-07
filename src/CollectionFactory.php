<?php

namespace Tyea\Aviator;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Sequentially;

class CollectionFactory
{
	private function __construct()
	{
	}

	public static function createFromArray(array $encodedConstraints, bool $allowMissingFields, bool $allowExtraFields): Collection
	{
		$fields = [];
		foreach ($encodedConstraints as $field => $encodedFieldConstraints) {
			$constraints = [];
			foreach ($encodedFieldConstraints as $encodedConstraint) {
				@list($constraint, $queryString) = explode("?", $encodedConstraint, 2);
				$options = [];
				parse_str($queryString ?? "", $options);
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