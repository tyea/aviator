<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class EndsWith extends Constraint
{
	public $message = "This value should end with \"{{ value }}\".";
	public $value = "";
}