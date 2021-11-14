<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class Alphanumeric extends Constraint
{
	public $message = "This value should be alphanumeric.";
	public $case = "mixed";
}