<?php

namespace LaxCorp\ApiBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @inheritdoc
 */
class LoginStatus {

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     *
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $login;

    /**
     * DATE_ATOM
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Serializer\SerializedName("tarificationDate")
     * @Serializer\Type("DateTime")
     */
    private $tarificationDate;

    /**
     * DATE_ATOM
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Serializer\SerializedName("expiredDate")
     * @Serializer\Type("DateTime")
     */
    private $expiredDate;

    /**
     * DATE_ATOM
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     *
     * @Serializer\SerializedName("accountProblemDate")
     * @Serializer\Type("DateTime")
     */
    private $accountProblemDate;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=19, scale=2, nullable=true)
     *
     * @Serializer\SerializedName("accountNeedSum")
     * @Serializer\Type("float")
     */
    private $accountNeedSum;

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     *
     * @return LoginStatus
     */
    public function setLogin(string $login): LoginStatus
    {
        $this->login = trim($login);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTarificationDate(): ?\DateTime
    {
        return $this->tarificationDate;
    }

    /**
     * @param \DateTime $tarificationDate
     *
     * @return LoginStatus
     */
    public function setTarificationDate(?\DateTime $tarificationDate): LoginStatus
    {
        $this->tarificationDate = $tarificationDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getExpiredDate(): ?\DateTime
    {
        return $this->expiredDate;
    }

    /**
     * @param \DateTime $expiredDate
     *
     * @return LoginStatus
     */
    public function setExpiredDate(?\DateTime $expiredDate): LoginStatus
    {
        $this->expiredDate = $expiredDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getAccountProblemDate(): ?\DateTime
    {
        return $this->accountProblemDate;
    }

    /**
     * @param \DateTime $accountProblemDate
     *
     * @return LoginStatus
     */
    public function setAccountProblemDate(?\DateTime $accountProblemDate): LoginStatus
    {
        $this->accountProblemDate = $accountProblemDate;

        return $this;
    }

    /**
     * @return float
     */
    public function getAccountNeedSum(): ?float
    {
        return $this->accountNeedSum;
    }

    /**
     * @param float $accountNeedSum
     *
     * @return LoginStatus
     */
    public function setAccountNeedSum(?float $accountNeedSum): LoginStatus
    {
        $this->accountNeedSum = $accountNeedSum;

        return $this;
    }

}