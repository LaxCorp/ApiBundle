<?php

namespace LaxCorp\ApiBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class InputInvoice
 *
 * @package LaxCorp\ApiBundle\Model
 * @ORM\Entity()
 */
class InputInvoice
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
    private $createdAt;

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
     * @var number
     *
     * @ORM\Column(type="decimal", precision=19, scale=2, nullable=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $paidAmount;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\Expose()
     */
    private $couteragentId;

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
    public function getPaidAmount()
    {
        return $this->paidAmount;
    }

    /**
     * @param number $paidAmount
     *
     * @return InputInvoice
     */
    public function setPaidAmount(number $paidAmount): InputInvoice
    {
        $this->paidAmount = $paidAmount;

        return $this;
    }

    /**
     * @return int
     */
    public function getCouteragentId()
    {
        return $this->couteragentId;
    }

    /**
     * @param int $couteragentId
     */
    public function setCouteragentId($couteragentId)
    {
        $this->couteragentId = $couteragentId;
    }

}