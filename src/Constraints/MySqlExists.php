<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class MySqlExists extends Constraint
{
	public $message = "This field is invalid.";
	public $table = "foos";
	public $column = "id";
}