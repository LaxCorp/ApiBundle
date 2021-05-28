<?php

namespace LaxCorp\ApiBundle\Model;

use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class InputPayment
 *
 * @package LaxCorp\ApiBundle\Model
 * @ORM\Entity()
 */
class InputPayment
{

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\ReadOnly()
     * @Serializer\Groups({"GetAction", "PatchAction"})
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
     * readOnly
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Serializer\SerializedName("created")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     * @Serializer\Groups({"GetAction"})
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\Groups({"GetAction", "PostAction", "PatchAction"})
     * @Serializer\Expose()
     */
    private $invoiceId;

    /**
     * @var number
     *
     * @ORM\Column(type="decimal", precision=19, scale=2, nullable=true)
     *
     * @Serializer\Type("double")
     * @Serializer\Groups({"GetAction", "PostAction"})
     * @Serializer\Expose()
     */
    private $amount;

    /**
     * @var number
     *
     * @ORM\Column(type="decimal", precision=19, scale=2, nullable=true)
     *
     * @Serializer\Type("double")
     * @Serializer\Groups({"GetAction", "PostAction"})
     * @Serializer\Expose()
     */
    private $comission;

    /**
     * @var string
     *
     * @ORM\Column(type="string", type="string", length=255, nullable=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Groups({"GetAction", "PostAction"})
     * @SWG\Property(example="BANK", description="BANK | ONLINE")
     * @Serializer\Expose()
     */
    private $paymentType;

    /**
     * @var string
     *
     * @ORM\Column(type="string", type="string", length=255, nullable=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Groups({"GetAction", "PostAction"})
     * @SWG\Property(example="REFILL", description="REFILL | MONEYBACK")
     * @Serializer\Expose()
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", type="string", length=255, nullable=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Groups({"GetAction"})
     * @Serializer\Expose()
     */
    private $description;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\Groups({"GetAction", "PostAction"})
     * @Serializer\Expose()
     * @Assert\NotNull()
     * @Assert\GreaterThan(value=0)
     */
    private $accountId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\Groups({"GetAction"})
     * @Serializer\Expose()
     */
    private $counteragentId;

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
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @param int $invoiceId
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
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
     * @return number
     */
    public function getComission()
    {
        return $this->comission;
    }

    /**
     * @param number $comission
     */
    public function setComission($comission)
    {
        $this->comission = $comission;
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param string $paymentType
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
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
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     *
     * @return InputPayment
     */
    public function setAccountId(int $accountId)
    {
        $this->accountId = $accountId;

        return $this;
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
     *
     * @return InputPayment
     */
    public function setCounteragentId(int $counteragentId)
    {
        $this->counteragentId = $counteragentId;

        return $this;
    }

}
