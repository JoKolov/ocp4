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
    			$session->getFlashBag()->add('success', "Formulaire validé");
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


    public function paymentAction(Request $request)
    {
        $session = $request->getSession();

        // no invoice to pay => redirect to tickets selection
        if (!$session->has('invoice'))
        {
            // redirect to home ticketing form
            return $this->redirectToRoute('jni_ticketing_home');
        }

        // amount calculations
        $admissionRateRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('JNiTicketingBundle:AdmissionRate');
        $listAdmissionRates = $admissionRateRepository->getListAdmissionRatesByAgeDESC();
        $listRedudecRates = $admissionRateRepository->getListAdmissionRatesByAgeDESC("reduced");

        $amountCalculator = $this->get('jni_ticketing.amount_calculator'); // requiring amountCalcultaor service
        $invoice = $session->get('invoice');
        // select rate for each visitor
        foreach ($invoice->getVisitors() as $visitor)
        {
            $rate = $amountCalculator->getVisitorAgeRate($visitor, $listAdmissionRates, $listRedudecRates);
            $visitor->setAdmissionRate($rate);
        }
        // total amount calculation
        $amount = $amountCalculator->getInvoiceAmount($invoice);
        
        // Payment check
        if ($request->isMethod('POST'))
        {
            /*
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist([$invoice]);
            $entityManager->flush();
            */
        }

        // display view : basket summary + stripe form
        return $this->render('JNiTicketingBundle:Booking:payment.html.twig', [
            'invoice'           => $session->get('invoice'),
            'stripePublicKey'   => $this->container->getParameter('stripe_public_key'),
            'amount'            => $amount * 100,
            'currency'          => "EUR"
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
