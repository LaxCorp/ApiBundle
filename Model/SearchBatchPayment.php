<?php

namespace LaxCorp\ApiBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 */
class SearchBatchPayment
{
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
     * @ORM\Column(name="created", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="updated", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     * @Serializer\Type("string")
     */
    private $comment;

    public function getUuid1c(): ?string
    {
        return $this->uuid1c;
    }

    public function setUuid1c(?string $uuid1c): SearchBatchPayment
    {
        $this->uuid1c = $uuid1c;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): SearchBatchPayment
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): SearchBatchPayment
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): SearchBatchPayment
    {
        $this->comment = $comment;

        return $this;
    }


}
