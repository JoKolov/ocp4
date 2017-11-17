<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class ArrayNotEmptyValidator extends ConstraintValidator
{
	public function validate($array, Constraint $constraint)
	{	
		if (empty($array) or empty($array[0]))
		{
			$this->context->addViolation($constraint->message);
		}
	}
}