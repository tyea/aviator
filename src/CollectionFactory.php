<?php

namespace Tyea\Aviator;

use Symfony\Component\Validator\Constraint;
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
				$constraints[] = CollectionFactory::createConstraint($constraint, $options);
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

	private static function createConstraint(string $constraint, array $options): Constraint
	{
		if (!str_starts_with($constraint, "\\")) {
			$constraint = "\\" . $constraint;
		}
		$namespaces = [
			"\\Symfony\\Component\\Validator\\Constraints",
			"\\Tyea\\Aviator\\Constraints",
			"\\"
		];
		foreach ($namespaces as $namespace) {
			$class = $namespace . $constraint;
			if (class_exists($class)) {
				return new $class($options);
			}
		}
	}
}