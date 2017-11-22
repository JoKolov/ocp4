<?php

namespace JNi\TicketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JNi\TicketingBundle\Entity\Invoice;
use JNi\TicketingBundle\Entity\Visitor;
use JNi\TicketingBundle\Entity\Payment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JNi\TicketingBundle\Form\InvoiceType;
use JNi\TicketingBundle\Form\VisitorType;


class BookingController extends Controller
{
    //\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
    public function indexAction(Request $request)
    {
        $session = $request->getSession();

        if ($session->has('invoice'))
        {
            $invoice = $session->get('invoice');
            $session->remove('invoice');
        }
        else
        {
            $invoice = new Invoice;
        }

    	// admission form creation
    	$formInvoice = $this->createForm(InvoiceType::class, $invoice);

    	//// form was sent
    	if ($request->isMethod('POST'))
    	{
    		// loading objects => Invoice / Visitor
    		$formInvoice->handleRequest($request);

    		// check if form is valid
    		if ($formInvoice->isValid())
    		{
                // calcul total amount for this invoice
                $amountCalculator = $this->get('ticketing.amount_calculator'); // requiring amountCalculator service
                $amount = $amountCalculator->getInvoiceAmount($invoice);
                $invoice->setAmount($amount);

                // joining invoice to each visitors
                $invoice->setInvoiceForVisitors();
               
                // saving Invoice in Session for next step : Payment
    			$session->set('invoice', $invoice);

    			// redirect to payment page
    			return $this->redirectToRoute('jni_ticketing_payment');
    		}
    	}

    	// 1st view or invalid form => show form
        return $this->render('JNiTicketingBundle:Booking:index.html.twig', [
            'formInvoice'   => $formInvoice->createView()
        ]);
    }



    //\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
    public function paymentAction(Request $request)
    {
        $session = $request->getSession();

        // no invoice to pay => redirect to tickets selection
        if (!$session->has('invoice'))
        {
            // redirect to home ticketing form
            return $this->redirectToRoute('jni_ticketing_home');
        }

        $invoice = $session->get('invoice');

        // Payment check
        if ($request->isMethod('POST'))
        {
            $stripeService = $this->get('ticketing.stripe');
            $stripeCharge = $stripeService->chargeInvoice($invoice);

            if (is_object($stripeCharge))
            {
                $invoice = $stripeCharge;
                 // payment confirmed
                $session->set('invoice', $invoice);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($invoice);
                $entityManager->flush();

                $session->getFlashBag()->add('alert', [
                    'type'      => 'success', 
                    'content'   => "Paiement validé"
                ]);

                // redirect to confirmation page
                return $this->redirectToRoute('jni_ticketing_order_confirmation', ['key' => $invoice->getHashedKey()]);
            }

            // error during stripe cherging process          
            $session->getFlashBag()->add('alert', $stripeCharge);
        }

        // display view : basket summary + stripe form
        return $this->render('JNiTicketingBundle:Booking:payment.html.twig', [
            'invoice'           => $invoice,
            'stripePublicKey'   => $this->container->getParameter('stripe_public_key')
        ]);
    }



    //\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
    public function confirmationAction($key, Request $request)
    {
        // removing invoice from session
        $session = $request->getSession();
        if ($session->has('invoice'))
        {
            // sending confirmation email
            $this->get('ticketing.email')->sendBookingConfirmation($session->get('invoice'));
            $session->remove('invoice');
        }

        // query invoice in db from its hashed key
        $repository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('JNiTicketingBundle:Invoice');
        $invoice = $repository->findOneBy(['hashedKey' => $key]);

        // invoice not found => redirect to ticketing home page
        if (is_null($invoice))
        {
            $session->getFlashBag()->add('alert', [
                'type'      => 'warning',
                'content'   => "La réservation demandée n'existe pas ou plus."
            ]);
            return $this->redirectToRoute('jni_ticketing_home');
        }

        // invoice found
        return $this->render('JNiTicketingBundle:Booking:confirmation.html.twig', [
            'invoice'       => $invoice
        ]);
    }


    //\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
    public function debugVar($var, $message = "")
    {
        echo $message;
        echo "<pre>";
        var_dump($var);
        echo "</pre>";
    }
}
