<?php
// src/JNi/TicketingBundle/Service/Payment/PaymentService.php

namespace JNi\TicketingBundle\Service\Payment;

use JNi\TicketingBundle\Entity\Invoice;
use JNi\TicketingBundle\Entity\Payment;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Error;

class PaymentService
{
	private $stripeSecretKey;
    private $errorCode;

	public function __construct($stripeSecretKey, $errorCode)
	{
		$this->stripeSecretKey = $stripeSecretKey;
        $this->errorCode = $errorCode;
	}

	/**
	 * Charge customer with stripe payment
	 */
	public function chargeInvoice(Invoice $invoice, $stripeToken)
	{
		try 
        {
            // stripe payment validation process
            Stripe::setApiKey($this->stripeSecretKey);

            $stripeCharge = Charge::create([
                'amount'    	=> $invoice->getAmount(),
                'currency'  	=> $invoice->getCurrency(),
                'description'	=> $invoice->getDescription(),
                'source' 		=> $stripeToken
            ]);

            $payment = new Payment;
            $payment->setStripeToken($stripeToken);
            $payment->setStripeId($stripeCharge->id);

            $invoice->generateHashedKey();
            $invoice->setPayment($payment);

            return $invoice;

        } // error throw payment process
        catch(Error\Card $e) 
        {
            throw new \Exception("Erreur ! la carte a été rejetée, aucune transaction n'a eu lieu. Vous pouvez réessayer.", $this->errorCode);
        }
        catch (Error\Base $e)
        {
            throw new \Exception("Erreur ! le paiement a été rejeté, aucune transaction n'a eu lieu. Vous pouvez réessayer.", $this->errorCode);
        }
        catch (\Exception $e)
        {
            throw new \Exception("Erreur ! le paiement a été rejeté, aucune transaction n'a eu lieu. Vous pouvez réessayer.", $this->errorCode);
        }
	}
}