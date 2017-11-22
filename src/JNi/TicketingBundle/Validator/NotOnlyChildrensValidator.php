<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class NotOnlyChildrensValidator extends ConstraintValidator
{
	public function validate($visitors, Constraint $constraint)
	{	
		$adult = false;
		foreach ($visitors as $visitor)
		{
			if ($visitor->getAge() >= 12)
			{
				$adult = true;
				break;
			}
		}

		if ($adult == false)
		{
			$this->context->addViolation($constraint->message);
		}
	}
}