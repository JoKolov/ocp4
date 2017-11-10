<?php
// src/JNi/TicketingBundle/Service/Amount/JNiAmountCalculator.php

namespace JNi\TicketingBundle\Service\Amount;

use JNi\TicketingBundle\Entity\Invoice;
use JNi\TicketingBundle\Entity\Visitor;
use JNi\TicketingBundle\Entity\AdmissionRate;


class JNiAmountCalculator
{
	/**
	 * Calculate amount from invoice
	 *
	 * @param   JNi\TicketingBundle\Entity\Invoice $invoice Invoice containing visitors info
	 * @return  double [<description>]
	 */
	public function getInvoiceAmount(Invoice $invoice)
	{
		$amount = 0;
		foreach ($invoice->getVisitors() as $visitor)
		{
			$amount += $visitor->getAdmissionRate();
		}
		return $amount;
	}

	public function getVisitorAgeRate(Visitor $visitor, Array $listAdmissionRates)
	{
		/*if (empty($listAdmissionRates) or !is_a($listAdmissionRates[0], 'AdmissionRate'))
		{
			throw new \Exception("JNiAmountCalculator->getVisitorRate() : \$listAdmissionRates ne contient pas des instances de AdmissionRate", 1);
		}*/

		foreach ($listAdmissionRates as $admissionRate)
		{
			if ($visitor->getAge() >= $admissionRate->getMinimumAge())
			{
				return $admissionRate->getRate();
			}
		}
	}
}