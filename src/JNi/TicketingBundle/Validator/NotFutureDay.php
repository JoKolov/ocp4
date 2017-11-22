<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotFutureDay extends Constraint
{
  public $message = "Date non valide";
}