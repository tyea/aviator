<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Exception;

class MatchesValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint): void
	{
		if (is_null($value) || (is_string($value) && $value == "")) {
			return;
		}
		$data = $this->context->getRoot();
		if (!array_key_exists($constraint->field, $data)) {
			throw new Exception();
		}
		if ($value != $data[$constraint->field]) {
			$this->context->buildViolation($constraint->message)->addViolation();
		}
	}
}