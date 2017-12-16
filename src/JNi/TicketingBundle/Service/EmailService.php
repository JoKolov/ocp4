<?php
// src/JNi/TicketingBundle/Service/EmailService.php

namespace JNi\TicketingBundle\Service;

class EmailService
{
	private $mailer;
	private $templating;


	public function __construct($mailer, $templating)
	{
		$this->mailer = $mailer;
		$this->templating = $templating;
	}

	/**
	 * Sending Email confirmation after invoice payment
	 * @param  \Entity\Invoice $invoice
	 */
	public function sendBookingConfirmation($invoice)
	{
        $message = (new \Swift_Message())
            ->setSubject("Confirmation Réservation Musée du Louvre")
            ->setFrom(['no-reply@musee.louvre.fr'   =>  "Musée du Louvre"])
            ->setTo([$invoice->getEmail()])
            ->setContentType('text/html')
        ;

        $message->setBody(
            $this->templating->render(
                'JNiTicketingBundle:Email:confirmation.html.twig', [
                    'invoice'   => $invoice
                ]
            ), 'text/html'
        );

        $this->mailer->send($message);
	}
}