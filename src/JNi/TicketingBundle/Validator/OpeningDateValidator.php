<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class OpeningDateValidator extends ConstraintValidator
{
	public function validate($date, Constraint $constraint)
	{
		// Invalid previous dates until today
		$today = new \DateTime;
		$today->setTime(0,0);
		if ($date < $today)
		{
			$this->context
				->buildViolation($constraint->message)
				->setParameter('{{ message }}', "Cette date n'est plus valide")
				->addViolation();
			return;
		}

		// Invalid dates on closure day : Tuesday + 1st May + 1st November + 25 December
		$closureDates = ["01/05", "01/11", "25/12"];
		$closureWeekDays = ["2"]; // 1 Monday -> 7 Sunday :: 2 = Tuesday
		
		if (in_array($date->format('d/m'), $closureDates) or in_array($date->format('N'), $closureWeekDays))
		{
			$this->context
				->buildViolation($constraint->message)
				->setParameter('{{ message }}', "Le musée est fermé le mardi ainsi qu'aux dates suivantes : 1er Mai, 1er Novembre et 25 Décembre")
				->addViolation();
			return;
		}

		// Invalid dates on specific days : Sunday + blank days
		$blankDays = ["01/01", "01/05", "08/05", "14/07", "15/08", "01/11", "11/11", "25/12"];
		$noBookingDay = ["7"]; // 1 Monday -> 7 Sunday
		
		if (in_array($date->format('d/m'), $blankDays) or in_array($date->format('N'), $noBookingDay))
		{
			$this->context
				->buildViolation($constraint->message)
				->setParameter('{{ message }}', "Le musée est fermé le mardi ainsi qu'aux dates suivantes : 1er Mai, 1er Novembre et 25 Décembre")
				->addViolation();
			return;
		}
	}
}