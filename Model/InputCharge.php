<?php

namespace LaxCorp\ApiBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class InputCharge
 *
 * @package LaxCorp\ApiBundle\Model
 * @ORM\Entity()
 */
class InputCharge
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Serializer\SerializedName("created")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Serializer\SerializedName("closed")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $closed;

    /**
     * @var number
     *
     * @ORM\Column(type="decimal", precision=19, scale=2, nullable=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\Expose()
     */
    private $accountId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\Expose()
     */
    private $counteragentId;

    /**
     * SUBSCRIPTION | OVERUSE_CLICKS | REFILL | MONEYBACK | PACKET_ACQUISITION
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="tariff_name", type="string", length=255, nullable=true)
     * @Serializer\SerializedName("tariff_name")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $tariffName;

    /**
     * @var int
     *
     * @ORM\Column(name="multiplier", type="integer")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $multiplier;

    /**
     * @var int
     *
     * @ORM\Column(name="clicksCount", type="integer")
     * @Serializer\SerializedName("clicks_count")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $clicksCount;


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
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * @param \DateTime $closed
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;
    }

    /**
     * @return number
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param number $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }

    /**
     * @return int
     */
    public function getCounteragentId()
    {
        return $this->counteragentId;
    }

    /**
     * @param int $counteragentId
     */
    public function setCounteragentId($counteragentId)
    {
        $this->counteragentId = $counteragentId;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getTariffName()
    {
        return $this->tariffName;
    }

    /**
     * @param string $tariffName
     */
    public function setTariffName($tariffName)
    {
        $this->tariffName = $tariffName;
    }

    /**
     * @return int
     */
    public function getMultiplier()
    {
        return $this->multiplier;
    }

    /**
     * @param int $multiplier
     */
    public function setMultiplier($multiplier)
    {
        $this->multiplier = $multiplier;
    }

    /**
     * @return int
     */
    public function getClicksCount()
    {
        return $this->clicksCount;
    }

    /**
     * @param int $clicksCount
     */
    public function setClicksCount($clicksCount)
    {
        $this->clicksCount = $clicksCount;
    }

}
