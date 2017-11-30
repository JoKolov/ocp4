<?php

namespace test\JNi\TicketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingControllerTest extends WebTestCase
{
	public function testShowTicketingHomePage()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/ticketing/');

		// assertion
		$this->assertGreaterThan(0, $crawler->filter('html:contains("Réservation de billets")')->count());
	}


	public function testRedirectToTicketiongHomePage()
	{
		$client = static::createClient();

		$crawler = $client->request('GET', '/ticketing/payment');

		// assertion
		$this->assertTrue($client->getResponse()->isRedirect('/ticketing/'));		
	}


	public function testVisitorAccessingToPaymentPage()
	{
		$client = static::createClient();
		$crawler = $client->request('GET', '/ticketing/');

		$buttonCrawlerNode = $crawler->selectButton('Valider');

		$form = $buttonCrawlerNode->form();

		$values = $form->getPhpValues();
		$values['jni_ticketingbundle_invoice']['date']	 	= "2018-07-13";	
		$values['jni_ticketingbundle_invoice']['halfDay']	= false;
		$values['jni_ticketingbundle_invoice']['email'] 	= "joffreynicoloff@gmail.com";
		$values['jni_ticketingbundle_invoice']['visitors'][0]['firstName'] 		= "Joffrey";
		$values['jni_ticketingbundle_invoice']['visitors'][0]['lastName'] 		= "Nicoloff";
		$values['jni_ticketingbundle_invoice']['visitors'][0]['country'] 		= "FR";
		$values['jni_ticketingbundle_invoice']['visitors'][0]['birthDate']	 	= "1986-06-24";
		$values['jni_ticketingbundle_invoice']['visitors'][0]['reducedRate']	= false;

		$crawler = $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

		// assertion
		$this->assertTrue($client->getResponse()->isRedirect('/ticketing/payment'));
	}
}