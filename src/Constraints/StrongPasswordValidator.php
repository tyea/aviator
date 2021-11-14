<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class StrongPasswordValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint): void
	{
		if (is_null($value) || (is_string($value) && $value == "")) {
			return;
		}
		$valid =
			preg_match("/[a-z]/", $value) &&
			preg_match("/[A-Z]/", $value) &&
			preg_match("/[0-9]/", $value) &&
			preg_match("/[!\"£$%^&*-+\\\\,.?\\/]/", $value);
		if (!$valid) {
			$this->context->buildViolation($constraint->message)->addViolation();
		}
	}
}