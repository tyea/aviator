<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class Slug extends Constraint
{
	public $message = "This value is not a valid slug.";
}