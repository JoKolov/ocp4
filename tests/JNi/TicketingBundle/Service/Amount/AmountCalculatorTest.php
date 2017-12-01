<?php

namespace test\JNi\TicketingBundle\Service;

use JNi\TicketingBundle\Service\Amount\AmountCalculator;
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
	 */
	public function testGetInvoiceAmount()
	{
		// museum visitors
		$visitor = new Visitor();		
		$visitor->setBirthDate(new \DateTime("1986-06-24"));

		// invoice creation
		$invoice = new Invoice();
		$invoice->setDate(new \DateTime("2018-01-04"));
		$invoice->addVisitor($visitor);

		// amount calculation
		$admissionRateRepository = $this->getAdmissionRateRepositoryMock();
		$amountCalculator = new AmountCalculator($admissionRateRepository);

		$amount = $amountCalculator->getInvoiceAmount($invoice);

		$normalAmount = 1600;

		// asserting amount value
		$this->assertEquals($normalAmount, $amount);
	}


	/**
	 * Mock
	 * @return AdmissionRateRepository Mock
	 */
	private function getAdmissionRateRepositoryMock()
	{
		// creating results for 
		// $admissionRateRepository->getListAdmissionRatesByAgeDESC()
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
		// $admissionRateRepository->getListAdmissionRatesByAgeDESC("reduced")
		$admissionRate = new admissionRate;
		$admissionRate
			->setMinimumAge(12)
			->setRate(1000);
		$admissionRatesByAgeDESCreduced = [$admissionRate];

		// creating Mock
		$admissionRateRepository = $this->createMock(AdmissionRateRepository::class);
		
		$admissionRateRepository
			->method('getListAdmissionRatesByAgeDESC')
			->will($this->onConsecutiveCalls($admissionRatesByAgeDESC, $admissionRatesByAgeDESCreduced));

		//var_dump($admissionRateRepository->getListAdmissionRatesByAgeDESC());
		//var_dump($admissionRateRepository->getListAdmissionRatesByAgeDESC("reduced")); die;

		return $admissionRateRepository;
	}
}