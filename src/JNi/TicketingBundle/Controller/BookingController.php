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

                $visitors = $invoice->getVisitors();
                $this->debugVar($invoice->getDate()->format('d/m/Y'), "Date du billet :");
                foreach ($visitors as $visitor) {
                    $this->debugVar($visitor->getBirthDate()->format('d/m/Y'), "Date de naissance :");
                    $this->debugVar($visitor->getAge($invoice->getDate()), "Age : ");
                }

    			// redirect to confirmation page
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

        // objects creation

        if (!$session->has('invoice'))
        {
            // redirect to home ticketing form
            return $this->redirectToRoute('jni_ticketing_home');
        }

        
        //\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
        // 1) Récupérer la liste des tarifs (collection de AdmissionRate)
        // 2) Pour chaque visiteur -> attribuer un tarif ($visitor->setAdmissionRate() EQUIV)
        // 3) Récupérer le total de la commande $amount = $this->get('JNiAmountCalculator')->getInvoiceAmount($invoice);
        //\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//\\//
        $admissionRateRepository = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('JNiTicketingBundle:AdmissionRate');
        $listAdmissionRates = $admissionRateRepository->getListAdmissionRatesByAgeDESC();

        $amountCalculator = $this->get('jni_ticketing.amount_calculator');

        $invoice = $session->get('invoice');
        foreach ($invoice->getVisitors() as $visitor)
        {
            $rate = $amountCalculator->getVisitorAgeRate($visitor, $listAdmissionRates);
            if ($visitor->getReducedRate() and $rate > 10) // A MODIFIER !!!! Le 10 est trop moche !!!!
            {
                $rate = 10;
            }
            $visitor->setAdmissionRate($rate);
        }
        
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
            'stripePublicKey' => $this->container->getParameter('stripe_public_key')
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
