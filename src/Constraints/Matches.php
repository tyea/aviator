<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class Matches extends Constraint
{
	public $message = "This field does not match {{ label }}.";
	public $field = "";
	public $label = "";
}