<?php

namespace LaxCorp\ApiBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Table(name="company_change_request")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessorOrder("custom", custom = {
 *     "id", "counteragentId"
 * })
 */
class SearchCompanyChangeRequest
{

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\ReadOnly()
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @Serializer\SerializedName("counteragent_id")
     * @Serializer\Type("integer")
     * @Serializer\Expose()
     */
    private $counteragentId;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=3, nullable=true)
     * @Serializer\Type("string")
     * @Serializer\SerializedName("country_code")
     * @Serializer\Expose()
     */
    private $countryCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $inn;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $kpp;

    /**
     * @var string
     *
     * @ORM\Column(name="reg_number", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $regNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_number", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $taxNumber;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\SerializedName("juridical_address")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $legalAddress;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default":false})
     * @Serializer\Type("boolean")
     * @Serializer\Expose()
     */
    private $completed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Serializer\SerializedName("created")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $description;

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
     * @return mixed
     */
    public function getCounteragentId()
    {
        return $this->counteragentId;
    }

    /**
     * @param mixed $counteragentId
     */
    public function setCounteragentId($counteragentId)
    {
        $this->counteragentId = $counteragentId;
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
    public function getLegalAddress()
    {
        return $this->legalAddress;
    }

    /**
     * @param string $legalAddress
     */
    public function setLegalAddress($legalAddress)
    {
        $this->legalAddress = $legalAddress;
    }

    /**
     * @return mixed
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param mixed $completed
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @return SearchCompanyChangeRequest
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
     * @return SearchCompanyChangeRequest
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
     * @return SearchCompanyChangeRequest
     */
    public function setTaxNumber($taxNumber)
    {
        $this->taxNumber = $taxNumber;

        return $this;
    }
}
