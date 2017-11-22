<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotBusyDay extends Constraint
{
  public $message = "Cette date n'est plus disponible, le musée a atteint sa capicité maximale";

  public function validatedBy()
  {
  	return 'ticketing.not_busy_day';
  }
}