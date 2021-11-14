<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class Contains extends Constraint
{
	public $message = "This value should contain \"{{ value }}\".";
	public $value = "";
}