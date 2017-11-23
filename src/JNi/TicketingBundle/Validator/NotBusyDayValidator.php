<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManagerInterface;
use JNi\TicketingBundle\Service\InvoiceRepositoryService;

class NotBusyDayValidator extends ConstraintValidator
{
	private $entityManager;
	private $visitorLimit;

	public function __construct(EntityManagerInterface $entityManager, $visitorLimit)
	{
		$this->entityManager = $entityManager;
		$this->visitorLimit = $visitorLimit;
	}

	public function validate($date, Constraint $constraint)
	{
		$nbVisitors = $this->entityManager
			->getRepository('JNiTicketingBundle:Visitor')
			->countForDay($date);
		if ($nbVisitors >= $this->visitorLimit)
		{
			$this->context->addViolation($constraint->message);
		}
	}
}