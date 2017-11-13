<?php

namespace JNi\TicketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table(name="ocp4_payment")
 * @ORM\Entity(repositoryClass="JNi\TicketingBundle\Repository\PaymentRepository")
 */
class Payment
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetimetz")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="stripeKey", type="string", length=255)
     */
    private $stripeKey;


    public function __construct()
    {
        $this->date = new \DateTime();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Payment
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set stripeKey
     *
     * @param string $stripeKey
     *
     * @return Payment
     */
    public function setStripeKey($stripeKey)
    {
        $this->stripeKey = $stripeKey;

        return $this;
    }

    /**
     * Get stripeKey
     *
     * @return string
     */
    public function getStripeKey()
    {
        return $this->stripeKey;
    }


    public function isValid()
    {
        return !is_null($this->getStripeKey());
    }
}

