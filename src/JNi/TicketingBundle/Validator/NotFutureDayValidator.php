<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class NotFutureDayValidator extends ConstraintValidator
{
	public function validate($date, Constraint $constraint)
	{
		// Invalid previous dates until today
		if ($date > new \DateTime)
		{
			$this->context
				->buildViolation($constraint->message)
				->addViolation();
			return;
		}
	}
}