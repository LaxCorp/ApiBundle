<?php

namespace LaxCorp\ApiBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 */
class InputBatchPayment
{

    /**
     * @var string
     * @Serializer\Type("string")
     * @SWG\Property(example="")
     * @Serializer\Groups({"GetAction", "PostAction"})
     */
    private $uuid1c;

    /**
     * @var InputPayment[]
     * @Serializer\Type("array<LaxCorp\ApiBundle\Model\InputPayment>")
     * @Serializer\Groups({"GetAction", "PostAction"})
     */
    private $payments;

    /**
     * @var string
     * @Serializer\Type("string")
     * @SWG\Property(example="")
     * @Serializer\Groups({"GetAction", "PostAction"})
     */
    private $comment;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
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
     * @return InputPayment[]
     */
    public function getPayments()
    {
        return $this->payments;
    }

    /**
     * @param InputPayment[] $payments
     *
     * @return InputBatchPayment
     */
    public function addPayments($payments)
    {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return InputBatchPayment
     */
    public function setComment(string $comment)
    {
        $this->comment = $comment;

        return $this;
    }

}
