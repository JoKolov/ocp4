<?php

namespace JNi\TicketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use JNi\TicketingBundle\Validator\NotPastDay;
use JNi\TicketingBundle\Validator\NotCloseDay;
use JNi\TicketingBundle\Validator\NotBlankDay;
use JNi\TicketingBundle\Validator\ArrayNotEmpty;
use JNi\TicketingBundle\Validator\HalfDayRequired;
use JNi\TicketingBundle\Validator\NotBusyDay;
use JNi\TicketingBundle\Validator\NotOnlyChildrens;

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
     * @Assert\NotBlank()
     * @Assert\Email(checkMX=true, message="Adresse email invalide, le domaine n'existe pas")
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @Assert\NotBlank()
     * @Assert\Date()
     * @NotPastDay()
     * @NotCloseDay()
     * @NotBlankDay()
     * @NotBusyDay()
     */
    private $date;

    /**
     * @var bool
     *
     * @ORM\Column(name="halfDay", type="boolean")
     * @Assert\Type("bool")
     * //@HalfDayRequired() Deactivated to perform auto set
     * please check method setHalfDay
     */
    private $halfDay;

    /**
     * @ORM\OneToOne(targetEntity="JNi\TicketingBundle\Entity\Payment", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $payment;

    /**
     * @ORM\OneToMany(targetEntity="JNi\TicketingBundle\Entity\Visitor", mappedBy="invoice", cascade={"persist"})
     * @ArrayNotEmpty(message="Au moins un visiteur doit être renseigné")
     * @Assert\Valid()
     * @NotOnlyChildrens()
     */
    private $visitors;

    /**
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->visitors = new ArrayCollection();
        $this->date = new \DateTime();
        $this->amount = 0;
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
        $now = new \DateTime;
        if (!$halfDay and $this->getDate()->format("d/m/Y") == $now->format("d/m/Y") and $now->format("H") >= 14)
        {
            $halfDay = true;
        }

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


    /**
     * get Currency for transaction // EUR
     * @return string EUR
     */
    public function getCurrency()
    {
        return "EUR";
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Invoice
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get amount with currency 10.00 EUR
     *
     * @return string
     */
    public function getAmountWithCurrency()
    {
        $amount = $this->amount / 100;
        return $amount . " " . $this->getCurrency();
    }

    /**
     * Get ticket Day Type : ex : day / half-day
     *
     * @return string
     */
    public function getTicketDayType()
    {
        if ($this->getHalfDay())
        {
            return "Demi-journée";
        }
        return "Journée";
    }


    /**
     * generate and set Invoice HAshedKey
     * @return string hashedKey
     */
    public function generateHashedKey()
    {
        $keyHead = ($this->getHalfDay()) ? "WH" : "WD";
        $keyContent = uniqid() . hash('md5', $this->getEmail());
        $keyContent = strtoupper($keyContent);
        $this->hashedKey = $keyHead . substr($keyContent, 0, 20);
        return $this->hashedKey;
    }


    /**
     * @return string small description of invoice
     */
    public function getDescription()
    {
        return $this->getEmail() . " :: " . count($this->getVisitors()) . " admissions :: Musée du Louvre";
    }


    public function setInvoiceForVisitors()
    {
        foreach ($this->getVisitors() as $visitor)
        {
            $visitor->setInvoice($this);
        }
    }
}
