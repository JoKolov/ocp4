<?php
// src/JNi/TicketingBundle/Service/AmountCalculator.php

namespace JNi\TicketingBundle\Service;

use JNi\TicketingBundle\Entity\Invoice;
use JNi\TicketingBundle\Entity\Visitor;
use JNi\TicketingBundle\Entity\AdmissionRate;
use JNi\TicketingBundle\Repository\AdmissionRateRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AmountCalculator
{
	private $admissionRateRepository;
	private $listAdmissionRates;

	public function __construct(AdmissionRateRepository $admissionRateRepository)
	{
		$this->admissionRateRepository = $admissionRateRepository;
	}


	/**
	 * Calculate amount for 1 invoice
	 *
	 * @param   Invoice $invoice 	Invoice containing visitors info
	 * @return  int 				Total amount (centimes)
	 */
	public function getInvoiceAmount(Invoice $invoice)
	{
		$amount = 0;
		foreach ($invoice->getVisitors() as $visitor)
		{
			$amount += $this->getVisitorRate($visitor);
		}
		return $amount;
	}


	/**
	 * Calculate rate for 1 visitor
	 * 
	 * @param  Visitor $visitor            
	 * @param  Array   $listAdmissionRates 
	 * @return Int                      	visitor admission rate
	 */
	private function getVisitorRate(Visitor $visitor)
	{
		if (is_null($this->listAdmissionRates))
		{
			// selecting every admission rates
	        $this->listAdmissionRates = [
	        	"standard"	=>	$this->admissionRateRepository->getListByAgeDESC(),
	        	"reduced"	=>	$this->admissionRateRepository->getListByAgeDESC("reduced")
	        ];
   		}

		// select standard admission rate with visitor Age
		foreach ($this->listAdmissionRates["standard"] as $admissionRate)
		{
			if ($visitor->getAge() >= $admissionRate->getMinimumAge())
			{
				$rate = $admissionRate->getRate();
				break;
			}
		}

		// check if reduced rate activated
		if (!$visitor->getReducedRate() or empty($this->listAdmissionRates["reduced"]))
		{
			return $rate;
		}

		// select reduced admission rate with visitor Age and control reduced rate is smaller than standard rate
		foreach ($this->listAdmissionRates["reduced"] as $admissionReducedRate)
		{
			if ($visitor->getAge() >= $admissionReducedRate->getMinimumAge() and $rate > $admissionReducedRate->getRate())
			{
				$rate = $admissionReducedRate->getRate();
				break;
			}
		}

		return $rate;
	}


	public function generateInvoiceAmount(Invoice $invoice)
	{
		$amount = 0;
		foreach ($invoice->getVisitors() as $visitor)
		{
			$visitor->setAdmissionRate($this->getVisitorRate($visitor));
			$amount += $visitor->getAdmissionRate();
		}
		$invoice->setAmount($amount);
		return $invoice;
	}

}