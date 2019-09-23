<?php

namespace LaxCorp\ApiBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class InputEvents
 *
 * @package LaxCorp\ApiBundle\Model
 */
class InputEvents
{

    /**
     * @var string
     *
     * @Serializer\Type("string")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Serializer\SerializedName("created")
     * @Serializer\Type("string")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Serializer\SerializedName("updated")
     * @Serializer\Type("string")
     */
    private $updatedAt;

    /**
     * @ORM\Column(name="processing", type="boolean", nullable=true, options={"default":false})
     * @Serializer\Type("boolean")
     */
    private $processing;

    /**
     * @ORM\Column(name="completed", type="boolean", nullable=true, options={"default":false})
     * @Serializer\Type("boolean")
     */
    private $completed;

    /**
     * @var string
     *
     * @ORM\Column(name="resource_name", type="string", length=255, nullable=true)
     * @Serializer\SerializedName("resource_name")
     * @Serializer\Type("string")
     */
    private $resourceName;

    /**
     * @var int
     *
     * @ORM\Column(name="resource_id", type="integer", nullable=true)
     * @Serializer\SerializedName("resource_id")
     * @Serializer\Type("string")
     */
    private $resourceId;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return InputEvents
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return InputEvents
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set processing
     *
     * @param boolean $processing
     *
     * @return InputEvents
     */
    public function setProcessing($processing)
    {
        $this->processing = $processing;

        return $this;
    }

    /**
     * Get processing
     *
     * @return boolean
     */
    public function getProcessing()
    {
        return $this->processing;
    }

    /**
     * Set completed
     *
     * @param boolean $completed
     *
     * @return InputEvents
     */
    public function setCompleted($completed)
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * Get completed
     *
     * @return boolean
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * Set resourceName
     *
     * @param string $resourceName
     *
     * @return InputEvents
     */
    public function setResourceName($resourceName)
    {
        $this->resourceName = $resourceName;

        return $this;
    }

    /**
     * Get resourceName
     *
     * @return string
     */
    public function getResourceName()
    {
        return $this->resourceName;
    }

    /**
     * Set resourceId
     *
     * @param integer $resourceId
     *
     * @return InputEvents
     */
    public function setResourceId($resourceId)
    {
        $this->resourceId = $resourceId;

        return $this;
    }

    /**
     * Get resourceId
     *
     * @return integer
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }
}
