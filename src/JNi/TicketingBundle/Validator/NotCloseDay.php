<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotCloseDay extends Constraint
{
  public $message = "La réservation en ligne n'est pas possible pour le {{ day }}";
}