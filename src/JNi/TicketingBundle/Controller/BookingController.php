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

        $invoice = new Invoice;

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
    			// Amount calculation
                // amount calculations
                $admissionRateRepository = $this
                    ->getDoctrine()
                    ->getManager()
                    ->getRepository('JNiTicketingBundle:AdmissionRate');
                $listAdmissionRates = $admissionRateRepository->getListAdmissionRatesByAgeDESC();
                $listRedudecRates = $admissionRateRepository->getListAdmissionRatesByAgeDESC("reduced");

                $amountCalculator = $this->get('jni_ticketing.amount_calculator'); // requiring amountCalcultaor service
                // select rate for each visitor
                foreach ($invoice->getVisitors() as $visitor)
                {
                    $rate = $amountCalculator->getVisitorAgeRate($visitor, $listAdmissionRates, $listRedudecRates);
                    $visitor->setAdmissionRate($rate);
                }

                // saving Invoice in Session for next step : Payment
    			$session->set('invoice', $invoice);

    			// redirect to payment page
    			return $this->redirectToRoute('jni_ticketing_payment');
    		}
    	}

    	if ($session->has('invoice'))
    	{
    		$session->remove('invoice');
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
        $amountCalculator = $this->get('jni_ticketing.amount_calculator'); // requiring amountCalcultaor service
        // total amount calculation
        $stripeAmount = $amountCalculator->getInvoiceAmount($invoice) * 100;

        // Payment check
        if ($request->isMethod('POST'))
        {
            try 
            {
                // stripe payment validation process
                \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_secret_key'));
                $stripeCustomer = \Stripe\Customer::create([
                    'email'   => $invoice->getEmail(),
                    'source'  => $request->request->get('stripeToken')
                ]);
                $stripeCharge = \Stripe\Charge::create([
                    'customer'  => $stripeCustomer->id,
                    'amount'    => $stripeAmount,
                    'currency'  => $invoice->getCurrency()
                ]);

                // payment confirmed
                $session->getFlashBag()->add('success', "Paiement validé");

                $payment = new Payment;
                $payment->setStripeKey($request->request->get('stripeToken'));
                $session->set('payment', $payment);

                /*
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist([$invoice]);
                $entityManager->flush();
                */
               $id = 0;
                
                // redirect to confirmation page
                return $this->redirectToRoute('jni_ticketing_order_confirmation', ['id' => $id]);

            } // error throw payment process
            catch (\Stripe\Error\Card $e)
            {
                $session->getFlashBag()->add('danger', "Erreur [strCard] le paiement a été refusé");
            }
            catch (Exception $e)
            {
                $session->getFlashBag()->add('danger', "Erreur ! le paiement a été refusé");
            }
        }

        // display view : basket summary + stripe form
        return $this->render('JNiTicketingBundle:Booking:payment.html.twig', [
            'invoice'           => $invoice,
            'stripePublicKey'   => $this->container->getParameter('stripe_public_key'),
            'amount'            => $stripeAmount
        ]);
    }



    //\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
    public function confirmationAction(Request $request)
    {
        $session = $request->getSession();
        $invoice = $session->get('invoice');
        $payment = $session->get('payment');
        $request->getSession()->remove('payment');

        $amountCalculator = $this->get('jni_ticketing.amount_calculator');
        $amount = $amountCalculator->getInvoiceAmount($invoice);

        /**
         * *****************************************
         * Envoyer Email
         * *****************************************
         */

        return $this->render('JNiTicketingBundle:Booking:confirmation.html.twig', [
            'invoice'       => $invoice,
            'payment'       => $payment,
            'amount'        => $amount
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
