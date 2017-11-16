<?php

namespace JNi\TicketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JNi\TicketingBundle\Validator\BirthDate;

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
     * @Assert\Type("string")
     * @Assert\Regex(
     *     pattern =     "/^[<>\{\}]+$/i",
     *     htmlPattern = "^[<>\{\}]+$",
     *     match=false,
     *     message = "Entrez un prénom valide (lettres MAJ/min, espace, tiret -, apostrophe")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="lastName", type="string", length=255)
     * @Assert\Type("string")
     * @Assert\Regex(
     *     pattern =     "/^[<>\{\}]+$/i",
     *     htmlPattern = "^[<>\{\}]+$",
     *     match=false,
     *     message = "Entrez un nom valide (lettres MAJ/min, espace, tiret -, apostrophe")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255)
     * @Assert\Country
     */
    private $country;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthDate", type="datetime")
     * @Assert\Date()
     * @BirthDate()
     */
    private $birthDate;

    /**
     * @var string
     *
     * @ORM\Column(name="admissionRate", type="decimal", precision=2, scale=0)
     */
    private $admissionRate;

    /**
     * @var boolean
     * @Assert\Type("bool")
     */
    private $reducedRate;

    /**
     * @ORM\ManyToOne(targetEntity="JNi\TicketingBundle\Entity\Invoice", inversedBy="visitors", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $invoice;


    public function __construct()
    {
        $this->reducedRate = false;
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
     * Set admissionRate
     *
     * @param boolean $reducedRate
     *
     * @return Visitor
     */
    public function setReducedRate($reducedRate)
    {
        $this->reducedRate = $reducedRate;

        return $this;
    }

    /**
     * Get reducedRate
     *
     * @return boolean
     */
    public function getReducedRate()
    {
        return $this->reducedRate;
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

    /**
     * Calculating visitor age referencing to specific date or actual date
     * @param  \DateTime|null $dateRef Date to compare with (default = now())
     * @return int  Age of visitor
     */
    public function getAge(\DateTime $dateRef = null)
    {
        $dateFormat = "d/m/Y";

        $birthDate = $this->getBirthDate();
        $dateRef = (is_null($dateRef)) ? new \DateTime() : $dateRef;

        // converting both dates to UTC timezone
        $birthDate->setTimezone(new \DateTimeZone('UTC'));
        $dateRef->setTimezone(new \DateTimeZone('UTC'));

        if ($birthDate > $dateRef) return 0;

        return (int) $birthDate->diff($dateRef)->format('%Y');
    }

    /**
     * @return string firstName (space) lastName
     */
    public function getFullName()
    {
        $fullname = $this->getFirstName() . ' ' . $this->getLastName();
        return $fullname;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return Visitor
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
}
