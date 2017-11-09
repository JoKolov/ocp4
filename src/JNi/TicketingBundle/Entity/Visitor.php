<?php

namespace JNi\TicketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Visitor
 *
 * @ORM\Table(name="ocp4_visitor")
 * @ORM\Entity(repositoryClass="JNi\TicketingBundle\Repository\VisitorRepository")
 */
class Visitor
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
     * @ORM\Column(name="firstName", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    private $lastName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthDate", type="datetime")
     */
    private $birthDate;

    /**
     * @var string
     *
     * @ORM\Column(name="admissionRate", type="decimal", precision=2, scale=0)
     */
    private $admissionRate;


    /**
     * @ORM\ManyToOne(targetEntity="JNi\TicketingBundle\Entity\Invoice", inversedBy="visitors")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invoice;

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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Visitor
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Visitor
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Visitor
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set admissionRate
     *
     * @param string $admissionRate
     *
     * @return Visitor
     */
    public function setAdmissionRate($admissionRate)
    {
        $this->admissionRate = $admissionRate;

        return $this;
    }

    /**
     * Get admissionRate
     *
     * @return string
     */
    public function getAdmissionRate()
    {
        return $this->admissionRate;
    }

    /**
     * Set invoice
     *
     * @param \JNi\TicketingBundle\Entity\Invoice $invoice
     *
     * @return Visitor
     */
    public function setInvoice(\JNi\TicketingBundle\Entity\Invoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \JNi\TicketingBundle\Entity\Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }
}
