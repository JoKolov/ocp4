<?php
// src/JNi/TicketingBundle/Service/Stripe/Stripe.php

namespace JNi\TicketingBundle\Service\Stripe;

use JNi\TicketingBundle\Entity\Invoice;
use JNi\TicketingBundle\Entity\Payment;
use Symfony\Component\HttpFoundation\Request;

class StripeService
{
	private $stripeSecretKey;
	private $request;

	public function __construct($stripeSecretKey)
	{
		$this->stripeSecretKey = $stripeSecretKey;
	}

	public function setRequest($request)
	{		
		$this->request = $request;
	}

	/**
	 * Charge customer with stripe payment
	 */
	public function chargeInvoice(Invoice $invoice)
	{
		try 
        {
            // stripe payment validation process
            \Stripe\Stripe::setApiKey($this->stripeSecretKey);

            $stripeCharge = \Stripe\Charge::create([
                'amount'    	=> $invoice->getAmount(),
                'currency'  	=> $invoice->getCurrency(),
                'description'	=> $invoice->getDescription(),
                'source' 		=> $this->request-> getCurrentRequest()->get('stripeToken')
            ]);

            $payment = new Payment;
            $payment->setStripeKey($this->request-> getCurrentRequest()->get('stripeToken'));
            $payment->setStripeId($stripeCharge->id);

            $invoice->generateHashedKey();
            $invoice->setPayment($payment);

            return $invoice;

        } // error throw payment process
        catch(\Stripe\Error\Card $e) 
        {
            return [
            	'type'      => 'danger',
                'content'   => "Erreur ! le paiement a été rejeté, aucune transaction n'a eu lieu. [rejected card]"
            ];
        }
        catch (\Stripe\Error\Base $e)
        {
            return [
                'type'      => 'danger',
                'content'   => "Erreur ! le paiement a été rejeté, aucune transaction n'a eu lieu."
            ];
        }
        catch (Exception $e)
        {
            return [
                'type'      => 'danger',
                'content'   => "Erreur ! le paiement a été rejeté, aucune transaction n'a eu lieu."
            ];
        }
	}
}