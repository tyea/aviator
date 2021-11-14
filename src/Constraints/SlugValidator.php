<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class SlugValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint): void
	{
		if (is_null($value) || (is_string($value) && $value == "")) {
			return;
		}
		$valid = preg_match("/^[a-z0-9]+(?:-[a-z0-9]+)*\$/", $value);
		if (!$valid) {
			$this->context->buildViolation($constraint->message)->addViolation();
		}
	}
}