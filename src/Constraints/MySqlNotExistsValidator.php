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
			"SELECT COUNT(`id`) FROM `%s` WHERE `%s` = ?;",
			$constraint->table,
			$constraint->column
		);
		$count = mysql()->column($query, [$value]);
		if ($count) {
			$this->context->buildViolation($constraint->message)->addViolation();
		}
	}
}