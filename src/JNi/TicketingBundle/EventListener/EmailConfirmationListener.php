<?php
// src/JNi/TicketingBundle/Listener/BookingListener.php

namespace JNi\TicketingBundle\EventListener;

use  Symfony\Component\HttpKernel\Event\PostResponseEvent;

class EmailConfirmationListener
{
	private $emailService;
	private static $invoice;

	public function __construct($emailService)
	{
		$this->emailService = $emailService;
	}

	public static function setInvoice($invoice)
	{
		self::$invoice = $invoice;
	}

	/**
	 * Sending Email confirmation after invoice payment
	 * @param  \Entity\Invoice $invoice
	 */
	public function sendEmail(PostResponseEvent $event)
	{
		if (is_null(self::$invoice))
		{
			return;
		}

		$invoice = self::$invoice;
		if (!is_null($invoice->getPayment()))
		{
			$this->emailService->sendBookingConfirmation($invoice);
			self::$invoice = null;
		}
	}
}