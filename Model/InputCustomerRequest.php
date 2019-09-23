<?php

namespace LaxCorp\ApiBundle\Model;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class InputCustomerRequest
 *
 * @package LaxCorp\ApiBundle\Model
 */
class InputCustomerRequest
{

    /**
     * ISO 8601 - 2017-10-20T11:27:25+07:00 or range (>=2017-09-26T18:28:07+07:00,<=2017-10-20T11:27:25+07:00)
     * @var string
     *
     * @ORM\Column(type="datetime")
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $customerLogin;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $name;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $jobEmail;

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     *
     * @return InputCustomerRequest
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerLogin()
    {
        return $this->customerLogin;
    }

    /**
     * @param string $customerLogin
     *
     * @return InputCustomerRequest
     */
    public function setCustomerLogin($customerLogin)
    {
        $this->customerLogin = $customerLogin;

        return $this;
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
     *
     * @return InputCustomerRequest
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
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
     *
     * @return InputCustomerRequest
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getJobEmail(): ?string
    {
        return $this->jobEmail;
    }

    /**
     * @inheritdoc
     */
    public function setJobEmail(?string $jobEmail)
    {
        $this->jobEmail = $jobEmail;

        return $this;
    }
}