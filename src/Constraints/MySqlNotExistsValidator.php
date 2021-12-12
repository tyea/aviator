<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Tyea\Aviator\MySql;

class MySqlNotExistsValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint): void
	{
		if (is_null($value) || (is_string($value) && $value == "")) {
			return;
		}
		$query = sprintf(
			"SELECT COUNT(`id`) FROM `%s` WHERE `%s` = ? AND `id` != ?;",
			$constraint->table,
			$constraint->column
		);
		$params = [
			$value,
			$constraint->ignore
		];
		$count = mysql()->value($query, $params);
		if ($count) {
			$this->context->buildViolation($constraint->message)->addViolation();
		}
	}
}