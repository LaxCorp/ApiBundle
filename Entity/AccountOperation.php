<?php

namespace LaxCorp\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * AccountOperation
 *
 * @ORM\Table(name="t_accountoperation")
 * @ORM\Entity()
 *
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessorOrder("custom", custom = {
 *     "id", "createdDate", "closedDate", "amount", "couteragentId", "reason"
 * })
 */
class AccountOperation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose()
     */
    private $id;

    /**
     * @var billjsonarray
     *
     * @ORM\Column(name="account", type="billjsonarray")
     */
    private $account;

    /**
     * @var billjsonarray
     *
     * @ORM\Column(name="customer", type="billjsonarray")
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="tariff_name", type="string", length=255, nullable=true)
     * @Serializer\SerializedName("tariff_name")
     * @Serializer\Type("string")
     * @Serializer\Expose()
     */
    private $tariffName;

    /**
     * @var string
     *
     * @ORM\Column(name="tariff_acquisition_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $tariffAcquisitionPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="tariff_subscription_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $tariffSubscriptionPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="tariff_billing_period", type="string", length=255, nullable=true)
     */
    private $tariffBillingPeriod;

    /**
     * @var int
     *
     * @ORM\Column(name="clicks_count", type="integer", nullable=true)
     * @Serializer\SerializedName("clicks_count")
     * @Serializer\Type("integer")
     * @Serializer\Expose()
     */
    private $clicksCount;

    /**
     * @var billjsonarray
     *
     * @ORM\Column(name="tariff", type="billjsonarray")
     */
    private $tariff;

    /**
     * @var timestampmills
     *
     * @ORM\Column(name="created", type="timestampmills")
     */
    private $created;

    /**
     * @var timestampmills
     *
     * @ORM\Column(name="closed", type="timestampmills")
     */
    private $closed;

    /**
     * @var string
     *
     * @ORM\Column(name="kind", type="string", length=255)
     */
    private $kind;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=255)
     * @Serializer\SerializedName("type")
     * @Serializer\Expose()
     */
    private $reason;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=2)
     * @Serializer\Expose()
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="commission", type="decimal", precision=10, scale=2)
     */
    private $commission;

    /**
     * @var int
     *
     * @ORM\Column(name="multiplier", type="integer")
     * @Serializer\Type("integer")
     * @Serializer\Expose()
     */
    private $multiplier;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     * @Serializer\Expose()
     */
    private $description;


    /**
     * @Serializer\VirtualProperty
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("couteragent_id")
     */
    public function getCouteragentId()
    {
        $account = $this->getAccount();
        if (!$account) {
            return null;
        }

        return $account['id'];
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("created")
     */
    public function getCreatedDate()
    {
        $created = $this->getCreated();
        if (!$created) {
            return null;
        }

        $dateTime = new \DateTime();
        $dateTime->setTimestamp($created/1000);

        return $dateTime;
    }

    /**
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("closed")
     */
    public function getClosedDate()
    {
        $closed = $this->getClosed();
        if (!$closed) {
            return null;
        }

        $dateTime = new \DateTime();
        $dateTime->setTimestamp($closed/1000);

        return $dateTime;
    }


    /**
     * @param integer $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set account
     *
     * @param billjsonarray $account
     *
     * @return AccountOperation
     */
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return billjsonarray
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set customer
     *
     * @param billjsonarray $customer
     *
     * @return AccountOperation
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return billjsonarray
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set tariff
     *
     * @param billjsonarray $tariff
     *
     * @return AccountOperation
     */
    public function setTariff($tariff)
    {
        $this->tariff = $tariff;

        return $this;
    }

    /**
     * Get tariff
     *
     * @return billjsonarray
     */
    public function getTariff()
    {
        return $this->tariff;
    }

    /**
     * Set created
     *
     * @param timestampmills $created
     *
     * @return AccountOperation
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return timestampmills
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set closed
     *
     * @param timestampmills $closed
     *
     * @return AccountOperation
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * Get closed
     *
     * @return timestampmills
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * Set kind
     *
     * @param string $kind
     *
     * @return AccountOperation
     */
    public function setKind($kind)
    {
        $this->kind = $kind;

        return $this;
    }

    /**
     * Get kind
     *
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * Set reason
     *
     * @param string $reason
     *
     * @return AccountOperation
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return AccountOperation
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set commission
     *
     * @param string $commission
     *
     * @return AccountOperation
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * Get commission
     *
     * @return string
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * Set multiplier
     *
     * @param integer $multiplier
     *
     * @return AccountOperation
     */
    public function setMultiplier($multiplier)
    {
        $this->multiplier = $multiplier;

        return $this;
    }

    /**
     * Get multiplier
     *
     * @return int
     */
    public function getMultiplier()
    {
        return $this->multiplier;
    }

    /**
     * Set clicksCount
     *
     * @param integer $clicksCount
     *
     * @return AccountOperation
     */
    public function setClicksCount($clicksCount)
    {
        $this->clicksCount = $clicksCount;

        return $this;
    }

    /**
     * Get clicksCount
     *
     * @return int
     */
    public function getClicksCount()
    {
        return $this->clicksCount;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return AccountOperation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getTariffAcquisitionPrice()
    {
        return $this->tariffAcquisitionPrice;
    }

    /**
     * @return string
     */
    public function getTariffBillingPeriod()
    {
        return $this->tariffBillingPeriod;
    }

    /**
     * @return string
     */
    public function getTariffName()
    {
        return $this->tariffName;
    }

    /**
     * @param string $tariffName
     *
     * @return $this
     */
    public function setTariffName($tariffName)
    {
        $this->tariffName = $tariffName;

        return $this;
    }

    /**
     * @param string $tariffAcquisitionPrice
     *
     * @return $this
     */
    public function setTariffAcquisitionPrice($tariffAcquisitionPrice)
    {
        $this->tariffAcquisitionPrice = $tariffAcquisitionPrice;

        return $this;
    }

    /**
     * @return string
     */
    public function getTariffSubscriptionPrice()
    {
        return $this->tariffSubscriptionPrice;
    }

    /**
     * @param string $tariffSubscriptionPrice
     *
     * @return $this
     */
    public function setTariffSubscriptionPrice($tariffSubscriptionPrice)
    {
        $this->tariffSubscriptionPrice = $tariffSubscriptionPrice;

        return $this;
    }

    /**
     * @param string $tariffBillingPeriod
     *
     * @return $this
     */
    public function setTariffBillingPeriod($tariffBillingPeriod)
    {
        $this->tariffBillingPeriod = $tariffBillingPeriod;

        return $this;
    }

}

