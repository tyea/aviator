<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class MySqlNotExists extends Constraint
{
	public $message = "This field is invalid.";
	public $table = "foos";
	public $column = "id";
}