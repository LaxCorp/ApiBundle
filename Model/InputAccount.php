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
class InputAccount
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

}
