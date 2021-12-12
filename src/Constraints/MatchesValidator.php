<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;

class MatchesValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint): void
	{
		if (is_null($value) || (is_string($value) && $value == "")) {
			return;
		}
		$data = $this->context->getRoot()->getData();
		$valid = $value == ($data[$constraint->field] ?? "");
		if (!$valid) {
			$this->context
				->buildViolation($constraint->message)
				->setParameter("{{ label }}", $constraint->label)
				->addViolation();
		}
	}
}