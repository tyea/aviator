<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Sequentially;

class CollectionFactory
{
	private function __construct()
	{
	}
	
	public static function createFromArray(array $rules, bool $missing, bool $extra): Collection
	{
		$fields = [];
		foreach ($rules as $field => $group) {
			$constraints = [];
			foreach ($group as $rule) {
				list($class, $options) = explode("?", $rule, 2);
				$options = $options ?? "";
				parse_str($options, $options);
				if (!class_exists($class)) {
					throw new Exception();
				}
				$constraints[] = new $class($options);
			}
			$fields[$field] = new Sequentially(["constraints" => $constraints]);
		}
		return new Collection([
			"fields" => $fields,
			"allowMissingFields" => $missing,
			"allowExtraFields" => $extra
		]);
	} 
}