<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ArrayNotEmpty extends Constraint
{
  public $message = "Empty value";
}