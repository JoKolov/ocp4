<?php

namespace JNi\TicketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JNi\TicketingBundle\Entity\Invoice;
use JNi\TicketingBundle\Entity\Visitor;
use JNi\TicketingBundle\Entity\Payment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JNi\TicketingBundle\Form\InvoiceType;


class BookingController extends Controller
{
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

    	$formInvoice = $this->createForm(InvoiceType::class, $invoice);


    	if ($request->isMethod('POST'))
    	{
    		$formInvoice->handleRequest($request);

    		if ($formInvoice->isValid())
    		{
                $invoice = $this->get('ticketing.amount_calculator')->generateInvoiceAmount($invoice);
                $invoice->setInvoiceForVisitors();

    			$session->set('invoice', $invoice);

    			return $this->redirectToRoute('jni_ticketing_payment');
    		}
    	}

    	// 1st view or invalid form => show form
        return $this->render('JNiTicketingBundle:Booking:index.html.twig', [
            'formInvoice'   => $formInvoice->createView(),
            'thread'        => [$this->generateUrl('jni_ticketing_home') => 'Musée du Louvre Paris'],
            'invalidDates'  => $this->get('ticketing.calendar')->getInvalidDates() // ** for datepicker
        ]);
    }



    public function paymentAction(Request $request)
    {
        $session = $request->getSession();

        if (!$session->has('invoice'))
        {
            return $this->redirectToRoute('jni_ticketing_home');
        }

        $invoice = $session->get('invoice');


        if ($request->isMethod('POST'))
        {
            try
            {
               $invoice = $this->get('ticketing.payment')->chargeInvoice($invoice, $request->request->get('stripeToken'));
            }
            catch (\Exception $e)
            {
                // error during payment charging process
                $errorMessage = [
                    'type'      => 'danger',
                    'content'   => $e->getMessage()
                ];
                $session->getFlashBag()->add('alert', $errorMessage);
                return $this->redirectToRoute('jni_ticketing_payment');
            }

            $session->set('invoice', $invoice);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($invoice);
            $entityManager->flush();

            $session->getFlashBag()->add('alert', [
                'type'      => 'success', 
                'content'   => "Paiement validé"
            ]);

            return $this->redirectToRoute('jni_ticketing_order_confirmation', ['key' => $invoice->getHashedKey()]);
        }

        return $this->render('JNiTicketingBundle:Booking:payment.html.twig', [
            'invoice'           => $invoice,
            'stripePublicKey'   => $this->container->getParameter('stripe_public_key'),
            'thread'            => [
                $this->generateUrl('jni_ticketing_home')    => 'Sélection de billets', 
                $this->generateUrl('jni_ticketing_payment') => 'Votre Panier']
        ]);
    }



    //\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
    public function confirmationAction($key, Request $request)
    {
        $session = $request->getSession();
        if ($session->has('invoice'))
        {
            $this->get('ticketing.email')->sendBookingConfirmation($session->get('invoice'));
            $session->remove('invoice');
        }

        $invoice = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('JNiTicketingBundle:Invoice')
            ->findOneBy(['hashedKey' => $key]);

        return $this->render('JNiTicketingBundle:Booking:confirmation.html.twig', [
            'invoice'       => $invoice,
            'thread'        => [
                $this->generateUrl('jni_ticketing_home') => 'Billetterie',
                $this->generateUrl('jni_ticketing_order_confirmation', ['key' => $invoice->getHashedKey()]) => 'Votre réservation'
            ]
        ]);
    }
}
