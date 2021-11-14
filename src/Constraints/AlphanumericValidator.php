<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Exception;

class AlphanumericValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint): void
	{
		if (is_null($value) || (is_string($value) && $value == "")) {
			return;
		}
		switch ($constraint->case) {
			case "upper":
				$valid = ctype_alnum($value) && ($value == strtoupper($value));
				break;
			case "lower":
				$valid = ctype_alnum($value) && ($value == strtolower($value));
				break;
			case "mixed":
				$valid = ctype_alnum($value);
				break;
			default:
				throw new Exception();
		}
		if (!$valid) {
			$this->context->buildViolation($constraint->message)->addViolation();
		}
	}
}