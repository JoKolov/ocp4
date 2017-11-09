<?php

namespace JNi\TicketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Invoice
 *
 * @ORM\Table(name="ocp4_invoice")
 * @ORM\Entity(repositoryClass="JNi\TicketingBundle\Repository\InvoiceRepository")
 */
class Invoice
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
     * @var string
     *
     * @ORM\Column(name="hashedKey", type="string", length=255, unique=true)
     */
    private $hashedKey;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="halfDay", type="boolean")
     */
    private $halfDay;

    /**
     * @ORM\OneToOne(targetEntity="JNi\TicketingBundle\Entity\Payment")
     * @ORM\JoinColumn(nullable=false)
     */
    private $payment;

    /**
     * @ORM\OneToMany(targetEntity="JNi\TicketingBundle\Entity\Visitor", mappedBy="invoice")
     */
    private $visitors;
    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->visitors = new ArrayCollection();
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
     * Set hashedKey
     *
     * @param string $hashedKey
     *
     * @return Invoice
     */
    public function setHashedKey($hashedKey)
    {
        $this->hashedKey = $hashedKey;

        return $this;
    }

    /**
     * Get hashedKey
     *
     * @return string
     */
    public function getHashedKey()
    {
        return $this->hashedKey;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Invoice
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Invoice
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
     * Set halfDay
     *
     * @param boolean $halfDay
     *
     * @return Invoice
     */
    public function setHalfDay($halfDay)
    {
        $this->halfDay = $halfDay;

        return $this;
    }

    /**
     * Get halfDay
     *
     * @return bool
     */
    public function getHalfDay()
    {
        return $this->halfDay;
    }

    /**
     * Set payment
     *
     * @param \JNi\TicketingBundle\Entity\Payment $payment
     *
     * @return Invoice
     */
    public function setPayment(\JNi\TicketingBundle\Entity\Payment $payment)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \JNi\TicketingBundle\Entity\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }


    /**
     * Add visitor
     *
     * @param \JNi\TicketingBundle\Entity\Visitor $visitor
     *
     * @return Invoice
     */
    public function addVisitor(\JNi\TicketingBundle\Entity\Visitor $visitor)
    {
        $this->visitors[] = $visitor;

        return $this;
    }

    /**
     * Remove visitor
     *
     * @param \JNi\TicketingBundle\Entity\Visitor $visitor
     */
    public function removeVisitor(\JNi\TicketingBundle\Entity\Visitor $visitor)
    {
        $this->visitors->removeElement($visitor);
    }

    /**
     * Get visitors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVisitors()
    {
        return $this->visitors;
    }
}