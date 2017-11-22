<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotOnlyChildrens extends Constraint
{
  public $message = "Les enfants de moins de 12 ans non accompagnés ne sont pas autorisés";
}