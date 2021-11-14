<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class StartsWith extends Constraint
{
	public $message = "This value should start with \"{{ value }}\".";
	public $value = "";
}