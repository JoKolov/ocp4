<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HalfDayRequired extends Constraint
{
  public $message = "A partir de 14h, le ticket demi-journée doit être sélectionné";
}