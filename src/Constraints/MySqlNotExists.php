<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class MySqlNotExists extends Constraint
{
	public $message = "This value is in use.";
	public $table = "foos";
	public $column = "id";
}