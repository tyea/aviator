<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class MySqlExists extends Constraint
{
	public $message = "This value is not in use.";
	public $table = "foos";
	public $column = "id";
}