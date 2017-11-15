<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class OpeningDate extends Constraint
{
  public $message = "{{ message }}";
}