<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class BirthDate extends Constraint
{
  public $message = "Date non valide";
}