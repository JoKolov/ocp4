<?php

namespace JNi\TicketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdmissionRate
 *
 * @ORM\Table(name="ocp4_admission_rate")
 * @ORM\Entity(repositoryClass="JNi\TicketingBundle\Repository\AdmissionRateRepository")
 */
class AdmissionRate
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="rate", type="decimal", precision=10, scale=0)
     */
    private $rate;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=255)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * 
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     * 
     */
    private $rateType;

    /**
     * @var int
     *
     * @ORM\Column(name="age_min", type="integer")
     * 
     */
    private $minimumAge;


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
     * Set name
     *
     * @param string $name
     *
     * @return AdmissionRate
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set rate
     *
     * @param string $rate
     *
     * @return AdmissionRate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return string
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return AdmissionRate
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return AdmissionRate
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set minimumAge
     *
     * @param int $minimumAge
     *
     * @return AdmissionRate
     */
    public function setMinimumAge($minimumAge = null)
    {
        $this->minimumAge = $minimumAge;

        return $this;
    }

    /**
     * Get minimumAge
     *
     * @return int
     */
    public function getMinimumAge()
    {
        return $this->minimumAge;
    }

    /**
     * Set rateType
     *
     * @param string $rateType
     *
     * @return AdmissionRate
     */
    public function setRateType($rateType)
    {
        $this->rateType = $rateType;

        return $this;
    }

    /**
     * Get rateType
     *
     * @return string
     */
    public function getRateType()
    {
        return $this->rateType;
    }
}
