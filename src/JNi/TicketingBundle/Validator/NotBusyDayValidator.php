<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use JNi\TicketingBundle\Service\InvoiceRepositoryService;

class NotBusyDayValidator extends ConstraintValidator
{
	private $entityManager;

	public function __construct(EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function validate($date, Constraint $constraint)
	{
		// more than 1000 visitors
		$nbVisitors = $this->entityManager
			->getRepository('JNiTicketingBundle:Invoice')
			->countVisitorsForDate($date);
		if ($nbVisitors >= 5)
		{
			$this->context->addViolation($constraint->message);
		}
	}
}