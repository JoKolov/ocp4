<?php

namespace test\JNi\TicketingBundle\Service;

use JNi\TicketingBundle\Service\AmountCalculator;
use JNi\TicketingBundle\Repository\AdmissionRateRepository;
use JNi\TicketingBundle\Entity\AdmissionRate;
use JNi\TicketingBundle\Entity\Invoice;
use JNi\TicketingBundle\Entity\Visitor;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AmountCalculatorTest extends TestCase
{
	/**
	 * TEST
	 * single visitor amount
	 */
	public function testGetInvoiceAmount()
	{
		$visitor = new Visitor();		
		$visitor->setBirthDate(new \DateTime("1986-06-24"));
		$amount = 1600;

		$invoice = new Invoice();
		$invoice->addVisitor($visitor);

		$admissionRateRepository = $this->getAdmissionRateRepositoryMock();
		$amountCalculator = new AmountCalculator($admissionRateRepository);

		// assertion : check amount value is correct
		$this->assertEquals($amount, $amountCalculator->getInvoiceAmount($invoice));
	}

	/**
	 * TEST
	 * familly visitor amount
	 */
	public function testFamillyAmountValue()
	{
		$visitors = [
			$this->getVisitor("1986-06-24", false), // 1600
			$this->getVisitor("1986-12-13", true),	// 1000
			$this->getVisitor("2011-10-04", false),	//  800
			$this->getVisitor("2011-10-11", true),	//  800
			$this->getVisitor("1951-10-10", false), // 1200
			$this->getVisitor("1952-07-26", true),	// 1000
			$this->getVisitor("2017-11-01", false)	//    0
		]; // 						Total Amount 	 = 6400
		$amount = 6400;
		$invoice = $this->getInvoice($visitors);
		$admissionRateRepository = $this->getAdmissionRateRepositoryMock();
		$amountCalculator = new AmountCalculator($admissionRateRepository);
		// assertion : check amount value is correct
		$this->assertEquals(
			$amount,
			$amountCalculator->getInvoiceAmount($invoice)
		);
	}

	/**
	 * TEST
	 * familly visitor amount
	 */
	public function testFamillyAmountValueGenerateMethod()
	{
		$visitors = [
			$this->getVisitor("1986-06-24", false), // 1600
			$this->getVisitor("1986-12-13", true),	// 1000
			$this->getVisitor("2011-10-04", false),	//  800
			$this->getVisitor("2011-10-11", true),	//  800
			$this->getVisitor("1951-10-10", false), // 1200
			$this->getVisitor("1952-07-26", true),	// 1000
			$this->getVisitor("2017-11-01", false)	//    0
		]; // 						Total Amount 	 = 6400
		$amount = 6400;

		$invoice = $this->getInvoice($visitors);
		$admissionRateRepository = $this->getAdmissionRateRepositoryMock();
		$amountCalculator = new AmountCalculator($admissionRateRepository);
		$invoice = $amountCalculator->generateInvoiceAmount($invoice);

		// assertion : check amount value is correct
		$this->assertEquals(
			$amount,
			$invoice->getAmount()
		);
	}


	/**
	 * Mock
	 * @return AdmissionRateRepository Mock
	 */
	private function getAdmissionRateRepositoryMock()
	{
		// creating results for 
		// $admissionRateRepository->getListByAgeDESC()
		$admissionRatesByAgeDESC = [];
		$ages 	= [60, 		12, 	4, 		0];
		$rates 	= [1200, 	1600, 	800, 	0];
		for ($i=0; $i < 4; $i++)
		{ 
			$admissionRate = new admissionRate;
			$admissionRate
				->setMinimumAge($ages[$i])
				->setRate($rates[$i]);
			$admissionRatesByAgeDESC[$i] = $admissionRate;
		}

		// creating results for
		// $admissionRateRepository->getListByAgeDESC("reduced")
		$admissionRate = new admissionRate;
		$admissionRate
			->setMinimumAge(12)
			->setRate(1000);
		$admissionRatesByAgeDESCreduced = [$admissionRate];

		// creating Mock
		$admissionRateRepository = $this->createMock(AdmissionRateRepository::class);
		
		$admissionRateRepository
			->method('getListByAgeDESC')
			->will($this->onConsecutiveCalls($admissionRatesByAgeDESC, $admissionRatesByAgeDESCreduced));

		return $admissionRateRepository;
	}

	/**
	 * @param  string  $birthDate   Visitor birth date : yyyy-mm-dd
	 * @param  boolean $reducedRate Require admission reduced rate
	 * @return Visitor              Instance of Visitor
	 */
	private function getVisitor($birthDate, $reducedRate = false)
	{
		$visitor = new Visitor();
		return $visitor
			->setBirthDate(new \DateTime($birthDate))
			->setReducedRate($reducedRate)
		;
	}

	/**
	 * @param  array  $visitors List of Intances of Visitor
	 * @return Invoice          Instance of Invoice
	 */
	private function getInvoice($visitors = [])
	{
		$invoice = new Invoice();
		foreach ($visitors as $visitor)
		{
			$invoice->addVisitor($visitor);
		}
		return $invoice;
	}
}