<?php

namespace LaxCorp\ApiBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class InputCounteragent
 *
 * @package LaxCorp\ApiBundle\Model
 * @ORM\Entity()
 */
class InputCounteragent
{

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\ReadOnly()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid1c", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $uuid1c;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=3, nullable=true)
     * @Serializer\Type("string")
     * @Serializer\SerializedName("country_code")
     */
    private $countryCode;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="inn", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $inn;

    /**
     * @var string
     *
     * @ORM\Column(name="kpp", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $kpp;

    /**
     * @var string
     *
     * @ORM\Column(name="reg_number", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $regNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_number", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $taxNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="juridical_address", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $juridicalAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="post_address", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $postAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_name", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $contactName;

    /**
     * ReadOnly
     *
     * @var string
     *
     * @ORM\Column(name="contact_email", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $contactEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_phone", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $contactPhone;

    /**
     * details of the organization filled
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     * @Serializer\Type("boolean")
     */
    private $dataChecked;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     * @Serializer\Type("boolean")
     */
    private $waspayment;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getUuid1c()
    {
        return $this->uuid1c;
    }

    /**
     * @param string $uuid1c
     */
    public function setUuid1c($uuid1c)
    {
        $this->uuid1c = $uuid1c;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * @param string $inn
     */
    public function setInn($inn)
    {
        $this->inn = $inn;
    }

    /**
     * @return string
     */
    public function getKpp()
    {
        return $this->kpp;
    }

    /**
     * @param string $kpp
     */
    public function setKpp($kpp)
    {
        $this->kpp = $kpp;
    }

    /**
     * @return string
     */
    public function getJuridicalAddress()
    {
        return $this->juridicalAddress;
    }

    /**
     * @param string $juridicalAddress
     */
    public function setJuridicalAddress($juridicalAddress)
    {
        $this->juridicalAddress = $juridicalAddress;
    }

    /**
     * @return string
     */
    public function getPostAddress()
    {
        return $this->postAddress;
    }

    /**
     * @param string $postAddress
     */
    public function setPostAddress($postAddress)
    {
        $this->postAddress = $postAddress;
    }

    /**
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * @param string $contactName
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;
    }

    /**
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * @param string $contactEmail
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;
    }

    /**
     * @return string
     */
    public function getContactPhone()
    {
        return $this->contactPhone;
    }

    /**
     * @param string $contactPhone
     */
    public function setContactPhone($contactPhone)
    {
        $this->contactPhone = $contactPhone;
    }

    /**
     * @return bool
     */
    public function isWaspayment()
    {
        return $this->waspayment;
    }

    /**
     * @param bool $waspayment
     */
    public function setWaspayment($waspayment)
    {
        $this->waspayment = $waspayment;
    }

    /**
     * @return bool
     */
    public function isDataChecked()
    {
        return $this->dataChecked;
    }

    /**
     * @param bool $dataChecked
     *
     * @return InputCounteragent
     */
    public function setDataChecked(bool $dataChecked): InputCounteragent
    {
        $this->dataChecked = $dataChecked;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }

    /**
     * @param string $countryCode
     *
     * @return InputCounteragent
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegNumber()
    {
        return $this->regNumber;
    }

    /**
     * @param string $regNumber
     *
     * @return InputCounteragent
     */
    public function setRegNumber($regNumber)
    {
        $this->regNumber = $regNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxNumber()
    {
        return $this->taxNumber;
    }

    /**
     * @param string $taxNumber
     *
     * @return InputCounteragent
     */
    public function setTaxNumber($taxNumber)
    {
        $this->taxNumber = $taxNumber;

        return $this;
    }
}