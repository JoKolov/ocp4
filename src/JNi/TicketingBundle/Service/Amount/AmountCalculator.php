<?php
// src/JNi/TicketingBundle/Service/Amount/AmountCalculator.php

namespace JNi\TicketingBundle\Service\Amount;

use JNi\TicketingBundle\Entity\Invoice;
use JNi\TicketingBundle\Entity\Visitor;
use JNi\TicketingBundle\Entity\AdmissionRate;
use JNi\TicketingBundle\Repository\AdmissionRateRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AmountCalculator
{
	private $admissionRateRepository;

	public function __construct(AdmissionRateRepository $admissionRateRepository)
	{
		$this->admissionRateRepository = $admissionRateRepository;
	}


	/**
	 * Calculate amount from invoice
	 *
	 * @param   JNi\TicketingBundle\Entity\Invoice $invoice Invoice containing visitors info
	 * @return  double [<description>]
	 */
	public function getInvoiceAmount(Invoice $invoice)
	{
		// selecting every admission rates
        $listAdmissionRates = [
        	"standard"	=>	$this->admissionRateRepository->getListAdmissionRatesByAgeDESC(),
        	"reduced"	=>	$this->admissionRateRepository->getListAdmissionRatesByAgeDESC("reduced")
        ];

        $rateCoeff = ($invoice->getHalfDay()) ? 0.5 : 1;
		$amount = 0;
		foreach ($invoice->getVisitors() as $visitor)
		{
			$amount += $this->getVisitorRate($visitor, $listAdmissionRates) * $rateCoeff;
		}
		return $amount;
	}


	private function getVisitorRate(Visitor $visitor, Array $listAdmissionRates)
	{
		// select standard admission rate with visitor Age
		foreach ($listAdmissionRates["standard"] as $admissionRate)
		{
			if ($visitor->getAge() >= $admissionRate->getMinimumAge())
			{
				$rate = $admissionRate->getRate();
				break;
			}
		}

		// check if reduced rate activated
		if (!$visitor->getReducedRate() or empty($listAdmissionRates["reduced"]))
		{
			return $rate;
		}

		// select reduced admission rate with visitor Age and control reduced rate is smaller than standard rate
		foreach ($listAdmissionRates["reduced"] as $admissionReducedRate)
		{
			if ($visitor->getAge() >= $admissionReducedRate->getMinimumAge() and $rate > $admissionReducedRate->getRate())
			{
				$rate = $admissionReducedRate->getRate();
				break;
			}
		}

		return $rate;
	}

}