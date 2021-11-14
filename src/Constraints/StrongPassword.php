<?php

namespace Tyea\Aviator\Constraints;

use Symfony\Component\Validator\Constraint;

class StrongPassword extends Constraint
{
	public $message = "This value must contain one lowercase character, uppercase character, numerical character, and special character.";
}