<?php

namespace LaxCorp\ApiBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class InputPatchEvents
 *
 * @package LaxCorp\ApiBundle\Model
 */
class InputPatchEvents
{

    /**
     * @ORM\Column(name="processing", type="boolean", nullable=true, options={"default":false})
     * @Serializer\Type("boolean")
     */
    private $processing = false;

    /**
     * @ORM\Column(name="completed", type="boolean", nullable=true, options={"default":false})
     * @Serializer\Type("boolean")
     */
    private $completed = false;

    /**
     * Set processing
     *
     * @param boolean $processing
     *
     * @return InputPatchEvents
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
     * @return InputPatchEvents
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
    
}
