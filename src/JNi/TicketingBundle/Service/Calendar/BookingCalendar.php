<?php
// src/JNi/TicketingBundle/Service/Amount/AmountCalculator.php

namespace JNi\TicketingBundle\Service\Calendar;

use JNi\TicketingBundle\Repository\InvoiceRepository;
use JNi\TicketingBundle\Repository\VisitorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class BookingCalendar
{
	private $invoiceRepository;
	private $visitorRepository;
	private $maxVisitorPerDay;

	public function __construct(InvoiceRepository $invoiceRepository, VisitorRepository $visitorRepository, $maxVisitorPerDay)
	{
		$this->invoiceRepository = $invoiceRepository;
		$this->visitorRepository = $visitorRepository;
		$this->maxVisitorPerDay = $maxVisitorPerDay;
	}


	public function getInvalidDates($date = null)
	{
		$date = (is_null($date)) ? new \DateTimeImmutable() : $date;
		return array_merge($this->getBlankDays(2, $date), $this->getBusyDates($date));
	}


	public function getInvalidWeekDays()
	{
		return [0,2]; // sunday + tuesday
	}


	public function getBusyDates($dayStart = null)
	{
		$maxDate = $this->invoiceRepository->getMaxDate();
        $date = (is_null($dayStart)) ? new \DateTime() : new \DateTime($dayStart->format('Y-m-d'));
        $dateInterval = new \DateInterval('P1D');
        $busyDates = [];

        while ($date <= $maxDate)
        {
            $nbVisitors = $this->visitorRepository->countForDay($date);
            if ($nbVisitors >= $this->maxVisitorPerDay)
            {
            	array_push($busyDates, new \DateTimeImmutable($date->format('Y-m-d')));
            }
            $date->add($dateInterval);
        }

        return $busyDates;
	}

	/**
	 * @param  int|null $yearsInterval nb of years blank days needed (default = 1)
	 * @param  datetime   $date        reference date to calculate blank days
	 * @return array                   list of blank dates
	 */
	public function getBlankDays(int $yearsInterval = null, $date = null)
	{
		$yearsInterval = (is_null($yearsInterval)) ? 1 : $yearsInterval;
		$date = (is_null($date)) ? new \DateTimeImmutable() : $date;
		$refDates = [$date];
		for ($i=1; $i <= $yearsInterval ; $i++)
		{ 
			array_push($refDates, $date->add(new \DateInterval('P'.$i.'Y')));
		}

		$blankDays = [];
		foreach ($refDates as $refDate)
		{
			array_push($blankDays, new \DateTimeImmutable($refDate->format('Y') . "-01-01")); 	// Jour de l'an
			array_push($blankDays, $this->getEasterMonday($refDate)); 							// Lundi de Pâques
			array_push($blankDays, new \DateTimeImmutable($refDate->format('Y') . "-05-01"));	// Fête du Travail
			array_push($blankDays, new \DateTimeImmutable($refDate->format('Y') . "-05-08"));	// 8 mai 1945
			array_push($blankDays, $this->getAscensionThirsday($refDate));						// Jeudi de l'Ascension
			array_push($blankDays, $this->getPentecoteMonday($refDate));						// Lundi de Pentecôte
			array_push($blankDays, new \DateTimeImmutable($refDate->format('Y') . "-07-14"));	// Fête Nationale
			array_push($blankDays, new \DateTimeImmutable($refDate->format('Y') . "-08-15"));	// Assomption
			array_push($blankDays, new \DateTimeImmutable($refDate->format('Y') . "-11-01"));	// La Toussaint
			array_push($blankDays, new \DateTimeImmutable($refDate->format('Y') . "-11-11"));	// Armistice
			array_push($blankDays, new \DateTimeImmutable($refDate->format('Y') . "-12-25"));	// Noël
		}
		return $blankDays;
	}


	private function getEasterDate($date = null)
	{
		$date = (is_null($date)) ? new \DateTimeImmutable() : $date;
		$easterDateRef = new \DateTimeImmutable($date->format('Y') . "-03-21");
		return $easterDateRef->add(new \DateInterval('P' . easter_days($date->format('Y')) . 'D'));
	}

	public function getEasterMonday($date = null)
	{
		$date = (is_null($date)) ? new \DateTimeImmutable() : $date;
		return $this->getEasterDate($date)->add(new \DateInterval('P1D'));
	}

	public function getAscensionThirsday($date = null)
	{
		$date = (is_null($date)) ? new \DateTimeImmutable() : $date;
		return $this->getEasterDate($date)->add(new \DateInterval('P39D'));
	}

	public function getPentecoteMonday($date = null)
	{
		$date = (is_null($date)) ? new \DateTimeImmutable() : $date;
		return $this->getEasterDate($date)->add(new \DateInterval('P50D'));
	}

}