<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotPastDay extends Constraint
{
  public $message = "Cette date n'est plus disponible";
}