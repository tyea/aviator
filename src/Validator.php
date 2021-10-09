<?php

namespace Tyea\Aviator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Sequentially;
use Symfony\Component\Validator\Validation as ValidatorFactory;
use Exception;

class Validator
{
	private function __construct()
	{
	}

	private static function contraint(string $rule): Constraint
	{
		list($name, $options) = explode("?", $rule, 2);
		$options = $options ?? "";
		parse_str($options, $options);
		$class = "Symfony\\Component\\Validator\\Constraints\\" . $name;
		if (!class_exists($class)) {
			throw new Exception();
		}
		return new $class($options);
	}

	public static function validate(array $data, array $rules, bool $missing = false, bool $extra = false): array
	{
		$fields = [];
		foreach ($rules as $field => $group) {
			$constraints = [];
			foreach ($group as $rule) {
				$constraints[] = Validator::contraint($rule);
			}
			$sequentially = new Sequentially(["constraints" => $constraints]);
			$fields[$field] = $sequentially;
		}
		$collection = new Collection([
			"fields" => $fields,
			"allowMissingFields" => $missing,
			"allowExtraFields" => $extra
		]);
		$validator = ValidatorFactory::createValidator();
		$violations = $validator->validate($data, $collection);
		$errors = [];
		if ($violations) {
			foreach ($violations as $violation) {
				$field = trim($violation->getPropertyPath(), "[]");
				$errors[$field] = $violation->getMessage();
			}
		}
		return $errors;
	}
}