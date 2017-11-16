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
		$easterMonday = $this->getEasterMondayFormated($date);
		$blankDays = [
			"01/01", 
			$this->getEasterMondayFormated($date),
			"01/05", 
			"08/05", 
			$this->getAscensionThirsdayFormated($date),
			$this->getPentecoteMondayFormated($date),
			"14/07", 
			"15/08", 
			"01/11", 
			"11/11", 
			"25/12"
		];
		$noBookingDay = ["7"]; // 1 Monday -> 7 Sunday
		
		if (in_array($date->format('d/m'), $blankDays) or in_array($date->format('N'), $noBookingDay))
		{
			$this->context
				->buildViolation($constraint->message)
				->setParameter('{{ message }}', "La réservation en ligne n'est pas possible pour les dimanches et les jours fériés")
				->addViolation();
			return;
		}
	}

	/**
	 * @param  integer $year (0000) year required to calcul easter date
	 * @return \DateTime     date of easter Monday
	 */
	private function getEasterDate($date)
	{
		$easterDate = new \DateTimeImmutable($date->format('Y') . "-03-21");
		return $easterDate->add(new \DateInterval('P' . easter_days($date->format('Y')) . 'D'));
	}

	private function getEasterMondayFormated($date, $format = "d/m")
	{
		return $this->getEasterDate($date)->add(new \DateInterval('P1D'))->format($format);
	}

	private function getAscensionThirsdayFormated($date, $format = "d/m")
	{
		return $this->getEasterDate($date)->add(new \DateInterval('P39D'))->format($format);
	}

	private function getPentecoteMondayFormated($date, $format = "d/m")
	{
		return $this->getEasterDate($date)->add(new \DateInterval('P50D'))->format($format);
	}
}