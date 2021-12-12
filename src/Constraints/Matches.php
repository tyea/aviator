<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class Matches extends Constraint
{
	public $message = "This value does not match.";
	public $field = "";
}