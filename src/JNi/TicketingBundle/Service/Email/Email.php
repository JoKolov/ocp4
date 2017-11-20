<?php
// src/JNi/TicketingBundle/Service/Email/Email.php

namespace JNi\TicketingBundle\Service\Email;

//use JNi\TicketingBundle\Entity\Invoice;

class Email
{
	private $mailer;
	private $router;
	private $templating;


	public function __construct($mailer, $router, $templating)
	{
		$this->mailer = $mailer;
		$this->router = $router;
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
                    'invoice'   => $invoice,
                    'urlConfirmation'   => $this->router->generate('jni_ticketing_order_confirmation', ['key' => $invoice->getHashedKey()])
                ]
            ), 'text/html'
        );

        $this->mailer->send($message);
	}
}