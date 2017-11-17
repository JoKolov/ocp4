<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class NotPastDayValidator extends ConstraintValidator
{
	public function validate($date, Constraint $constraint)
	{
		// Invalid previous dates until today
		$today = new \DateTime;
		$today->setTime(0,0);
		if ($date < $today)
		{
			$this->context->addViolation($constraint->message);
		}
	}
}