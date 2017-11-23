<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class HalfDayRequiredValidator extends ConstraintValidator
{
	public function validate($halfDay, Constraint $constraint)
	{
		$now = new \DateTime;
        if (!$halfDay and $this->context->getObject()->getDate()->format("d/m/Y") == $now->format("d/m/Y") and $now->format("H") >= 14)
        {
            $this->context->addViolation($constraint->message);
        }
	}
}