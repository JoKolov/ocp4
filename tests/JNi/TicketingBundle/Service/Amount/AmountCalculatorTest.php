<?php

namespace test\JNi\TicketingBundle\Service;

use JNi\TicketingBundle\Service\Amount\AmountCalculator;
use JNi\TicketingBundle\Entity\Invoice;
use JNi\TicketingBundle\Entity\Visitor;
use PHPUnit\Framework\TestCase;

require_once 'D:\JNiWeb\_FTP_PHPNET\www\_openclassrooms\cpm-dev-p04\MuseeDuLouvre\app\AppKernel.php';

class AmountCalculatorTest extends TestCase
{
	public function testGetInvoiceAmount()
	{
		// symfony req
		$kernel = new \AppKernel('dev', true);
		$kernel->boot();
		$container = $kernel->getContainer();

		// museum visitors
		$visitor = new Visitor();		
		$visitor->setBirthDate(new \DateTime("1986-06-24"));

		// invoice creation
		$invoice = new Invoice();
		$invoice->setDate(new \DateTime("2018-01-04"));
		$invoice->addVisitor($visitor);

		// amount calculation
		/*$admissionRateRepository = $this->getMockBuilder('JNi\TicketingBundle\Repository\AdmissionRateRepository')
			//->disableOriginalConstructor()
			->getMock();
		$amountCalculator = new AmountCalculator($admissionRateRepository);*/
		$amountCalculator = $container->get('ticketing.amount_calculator');
		$amount = $amountCalculator->getInvoiceAmount($invoice);

		// asserting amount value
		$this->assertEquals(1600, $amount);
	}
}