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

	public function getVisitorAgeRate(Visitor $visitor, Array $listAdmissionRates, Array $listAdmissionReducedRates = [])
	{
		/*$admissionRateClassName = 'AdsmissionRate';
		if (empty($listAdmissionRates) or !$listAdmissionRates[0] instanceof $admissionRateClassName)
		{
			throw new \Exception("JNiAmountCalculator->getVisitorRate() : \$listAdmissionRates est vide ou ne contient pas des instances de AdmissionRate", 1);
		}*/

		foreach ($listAdmissionRates as $admissionRate)
		{
			if ($visitor->getAge() >= $admissionRate->getMinimumAge())
			{
				$rate = $admissionRate->getRate();
				break;
			}
		}

		if (!$visitor->getReducedRate() or empty($listAdmissionReducedRates))
		{
			return $rate;
		}

		foreach ($listAdmissionReducedRates as $admissionReducedRate)
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