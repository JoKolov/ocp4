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
                    $rateCoeff = ($invoice->getHalfDay()) ? 0.5 : 1;
                    $rate = $amountCalculator->getVisitorAgeRate($visitor, $listAdmissionRates, $listRedudecRates) * $rateCoeff;
                    $visitor->setAdmissionRate($rate);
                    $visitor->setInvoice($invoice);
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

        if ($stripeAmount <= 0)
        {
            $session->getFlashBag()->add('alert', [
                'type'      => 'warning',
                'content'   => 'Au moins une entrée payante est requise pour réserver.'
            ]);
            return $this->redirectToRoute('jni_ticketing_home');
        }

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
                $session->getFlashBag()->add('alert', [
                    'type'      => 'success', 
                    'content'   => "Paiement validé"
                ]);

                $payment = new Payment;
                $payment->setStripeKey($request->request->get('stripeToken'));
                $invoice->setHashedKey(hash('sha256', 'LouvreTicket' . $payment->getStripeKey() . $invoice->getEmail()));

                $invoice->setPayment($payment);
                $session->set('invoice', $invoice);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($invoice);
                $entityManager->flush();

                // sending confirmation email
                $mailer = $this->get('mailer');
                $message = (new \Swift_Message())
                    ->setSubject("Confirmation Réservation Musée du Louvre")
                    ->setFrom(['no-reply@musee.louvre.fr'   =>  "Musée du Louvre"])
                    ->setTo([$invoice->getEmail()])
                    ->setContentType('text/html')
                ;

                $message->setBody(
                    $this->renderView(
                        'JNiTicketingBundle:Email:confirmation.html.twig', [
                            'invoice'   => $invoice,
                            'amount'    => $stripeAmount / 100,
                            'urlConfirmation'   => $this->generateUrl('jni_ticketing_order_confirmation', ['key' => $invoice->getHashedKey()])
                        ]
                    ), 'text/html'
                );

                $mailer->send($message);
                
                // redirect to confirmation page
                return $this->redirectToRoute('jni_ticketing_order_confirmation', ['key' => $invoice->getHashedKey()]);

            } // error throw payment process
            catch(\Stripe\Error\Card $e) 
            {
                $session->getFlashBag()->add('alert', [
                    'type'      => 'danger',
                    'content'   => "Erreur ! le paiement a été rejeté, aucune transaction n'a eu lieu. [rejected card]"
                ]);
            }
            catch (\Stripe\Error\Base $e)
            {
                $session->getFlashBag()->add('alert', [
                    'type'      => 'danger',
                    'content'   => "Erreur ! le paiement a été rejeté, aucune transaction n'a eu lieu."
                ]);
            }
            catch (Exception $e)
            {
                $session->getFlashBag()->add('alert', [
                    'type'      => 'danger',
                    'content'   => "Erreur ! le paiement a été rejeté, aucune transaction n'a eu lieu."
                ]);
            }
        }

        // display view : basket summary + stripe form
        return $this->render('JNiTicketingBundle:Booking:payment.html.twig', [
            'invoice'           => $invoice,
            'stripePublicKey'   => $this->container->getParameter('stripe_public_key'),
            'amount'            => $stripeAmount / 100,
            'stripeAmount'      => $stripeAmount
        ]);
    }



    //\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
    public function confirmationAction($key, Request $request)
    {
        // removing invoice from session
        $session = $request->getSession();
        if ($session->has('invoice'))
        {
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
        // amount calculation
        $amountCalculator = $this->get('jni_ticketing.amount_calculator');
        $amount = $amountCalculator->getInvoiceAmount($invoice);

        return $this->render('JNiTicketingBundle:Booking:confirmation.html.twig', [
            'invoice'       => $invoice,
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
