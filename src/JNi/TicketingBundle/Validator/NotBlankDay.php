<?php

namespace JNi\TicketingBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotBlankDay extends Constraint
{
  public $message = "La réservation en ligne n'est pas possible pour les jours fériés ({{ day }})";
}