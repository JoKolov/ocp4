<?php
// src/JNi/TicketingBundle/DataFixtures/ORM/LoadAmissionRate.php
 
namespace JNi\TicketingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use JNi\TicketingBundle\Entity\AdmissionRate;

class LoadAdmissionRate implements FixtureInterface
{
	public function load(ObjectManager $manager)
	{
		$admissionRateDatas = [
			'normal'	=> [
				'rate'					=> 16,
				'description'			=> "à partir de 12 ans",
				'minimumAge' 			=> 12
			],
			'enfant'	=> [
				'rate'					=> 8,
				'description'			=> "à partir de 4 ans et jusqu’à 12 ans",
				'minimumAge' 			=> 4
			],
			'senior'	=> [
				'rate'					=> 12,
				'description'			=> "à partir de 60 ans",
				'minimumAge' 			=> 60
			],
			'gratuit'	=> [
				'rate'					=> 0,
				'description'			=> "moins de 4 ans",
				'minimumAge' 			=> 0
			],
			"réduit"	=> [
				'rate'					=> 10,
				'description'			=> "accordé dans certaines conditions (étudiant, employé du musée, d’un service du Ministère de la Culture, militaire…)",
				'minimumAge' 			=> null
			]
		];

		foreach ($admissionRateDatas as $name => $datas)
		{
			$admissionRate = new AdmissionRate;
			$admissionRate->setName($name);
			$admissionRate->setRate($datas['rate']);
			$admissionRate->setCurrency('EUR');
			$admissionRate->setDescription($datas['description']);
			$admissionRate->setMinimumAge($datas['minimumAge']);
			$manager->persist($admissionRate);
		}

		$manager->flush();
	}
}