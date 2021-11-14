<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class StartsWithValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint): void
	{
		if (is_null($value) || (is_string($value) && $value == "")) {
			return;
		}
		if (!str_starts_with($value, $constraint->value)) {
			$this->context
				->buildViolation($constraint->message)
				->setParameter("{{ value }}", $constraint->value)
				->addViolation();
		}
	}
}