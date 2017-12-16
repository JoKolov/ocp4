<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class NotBlankDayValidator extends ConstraintValidator
{
	private $dayFormat = "d/m";

	public function validate($date, Constraint $constraint)
	{
		// Invalid dates on specific days : Sunday + blank days
		$easterMonday = $this->getEasterMondayFormated($date);
		$blankDays = [
			"01/01"	=> "Jour de l'an", 
			$this->getEasterMondayFormated($date)	=> "Lundi de Pâques",
			"01/05"	=> "Fête du Travail", 
			"08/05"	=> "8 Mai 1945", 
			$this->getAscensionThirsdayFormated($date)	=> "Jeudi de l'Ascension",
			$this->getPentecoteMondayFormated($date)	=> "Lundi de Pentecôte",
			"14/07"	=> "Fête Nationale", 
			"15/08"	=> "Assomption", 
			"01/11"	=> "La Toussaint", 
			"11/11"	=> "Armistice", 
			"25/12"	=> "Noël"
		];
		
		if (array_key_exists($date->format($this->dayFormat), $blankDays))
		{
			$this->context
				->buildViolation($constraint->message)
				->setParameter('{{ day }}', $blankDays[$date->format($this->dayFormat)])
				->addViolation();
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

	private function getEasterMondayFormated($date)
	{
		return $this->getEasterDate($date)->add(new \DateInterval('P1D'))->format($this->dayFormat);
	}

	private function getAscensionThirsdayFormated($date)
	{
		return $this->getEasterDate($date)->add(new \DateInterval('P39D'))->format($this->dayFormat);
	}

	private function getPentecoteMondayFormated($date)
	{
		return $this->getEasterDate($date)->add(new \DateInterval('P50D'))->format($this->dayFormat);
	}

	
}