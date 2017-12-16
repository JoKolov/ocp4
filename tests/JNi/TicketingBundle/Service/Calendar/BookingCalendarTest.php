<?php

namespace test\JNi\TicketingBundle\Service;

use JNi\TicketingBundle\Service\Calendar\BookingCalendar;
use JNi\TicketingBundle\Repository\InvoiceRepository;
use JNi\TicketingBundle\Repository\VisitorRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingCalendarTest extends TestCase
{
	// maximum booked date
	private $dayInterval = 10;
	private $maxVisitorPerDay = 1000;


	public function testGetBusyDates()
	{
		$bookingCalendar = new BookingCalendar($this->getInvoiceRepositoryMock(), $this->getVisitorRepositoryMock(), $this->maxVisitorPerDay);

		$this->assertEquals($this->getBusyDatesValid(), $bookingCalendar->getBusyDates());
	}


	private function getInvoiceRepositoryMock()
	{
		$invoiceRepository = $this->createMock(InvoiceRepository::class);
		
		$invoiceRepository
			->method('getMaxDate')
			->willReturn($this->getMaxDate());

		return $invoiceRepository;
	}


	private function getVisitorRepositoryMock()
	{
		$result = "";
		$interval = 0;
		while ($interval <= $this->dayInterval - 1)
		{
			// we want results as follow :
			// max visitors
			// 0 visitors
			// in loop
			$nbOfVisitors = ($interval%2) ? $this->maxVisitorPerDay : 0;
			$result .= ($interval < $this->dayInterval - 1) ? $nbOfVisitors . ',' : $nbOfVisitors;
			$interval++;
		}

		$visitorRepository = $this->createMock(VisitorRepository::class);

		$visitorRepository
			->method('countForDay')
			->will($this->onConsecutiveCalls(
				0,
				$this->maxVisitorPerDay,
				0,
				$this->maxVisitorPerDay,
				0,
				$this->maxVisitorPerDay,
				0,
				$this->maxVisitorPerDay,
				0,
				$this->maxVisitorPerDay));

		return $visitorRepository;
	}


	private function getBusyDatesValid()
	{
		$busyDates = [];

		$interval = 0;
		while ($interval <= $this->dayInterval - 1)
		{
			// we want results as follow :
			// max visitors
			// 0 visitors
			// in loop
			if ($interval%2)
			{
				array_push($busyDates, new \DateTimeImmutable($this->getStartDate()->add(new \DateInterval('P'.$interval.'D'))->format('Y-m-d')));
			}
			$interval++;
		}
		return $busyDates;	
	}


	private function getStartDate()
	{
		return new \DateTimeImmutable();
	}


	private function getMaxDate()
	{
		return new \DateTimeImmutable($this->getStartDate()->add(new \DateInterval('P'.$this->dayInterval.'D'))->format('Y-m-d'));
	}
}