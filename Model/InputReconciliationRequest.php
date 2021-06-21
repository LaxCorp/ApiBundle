<?php

namespace LaxCorp\ApiBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class InputReconciliationRequest
 *
 * @package LaxCorp\ApiBundle\Model
 * @ORM\Entity()
 */
class InputReconciliationRequest
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
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\Expose()
     */
    private $counteragentId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_from", type="datetime")
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $dateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_to", type="datetime")
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $dateTo;

    /**
     * @ORM\Column(name="completed", type="boolean", nullable=true, options={"default":false})
     *
     * @Serializer\Type("boolean")
     * @Serializer\Expose()
     */
    private $completed;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $created;


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
     * @return \DateTime
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * @param \DateTime $dateFrom
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }

    /**
     * @return \DateTime
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * @param \DateTime $dateTo
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
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


}
