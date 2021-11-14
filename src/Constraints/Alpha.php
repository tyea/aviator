<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class Alpha extends Constraint
{
	public $message = "This value should be alphabetical.";
	public $case = "mixed";
}