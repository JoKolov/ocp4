<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class NotCloseDayValidator extends ConstraintValidator
{
	public function validate($date, Constraint $constraint)
	{
		// Invalid dates on closure day : Tuesday + 1st May + 1st November + 25 December
		$closeDays = [
			"mardi" => 2, 
			"dimanche" => 7
		]; // 1 Monday -> 7 Sunday :: 2,7 = Tuesday, Sunday
		
		if (in_array($date->format('N'), $closeDays))
		{
			$this->context
				->buildViolation($constraint->message)
				->setParameter('{{ day }}', array_search($date->format('N'), $closeDays))
				->addViolation();
		}
	}
}