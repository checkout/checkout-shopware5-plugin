<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Models\Configuration;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_plugin_cko_sepa_configuration")
 */
class SepaConfiguration extends ModelEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="shop_id", type="string", nullable=false)
     */
    private $shopId;

    /**
     * @var string
     *
     * @ORM\Column(name="mandate_creditor_name", type="string")
     */
    private $mandateCreditorName;

    /**
     * @var string
     *
     * @ORM\Column(name="mandate_creditor_id", type="string")
     */
    private $mandateCreditorId;

    /**
     * @var string
     *
     * @ORM\Column(name="mandate_creditor_address_first", type="string")
     */
    private $mandateCreditorAddressFirst;

    /**
     * @var string
     *
     * @ORM\Column(name="mandate_creditor_address_second", type="string")
     */
    private $mandateCreditorAddressSecond;

    /**
     * @var string
     *
     * @ORM\Column(name="mandate_creditor_country", type="string")
     */
    private $mandateCreditorCountry;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getShopId(): string
    {
        return $this->shopId;
    }

    /**
     * @param string $shopId
     */
    public function setShopId(string $shopId): void
    {
        $this->shopId = $shopId;
    }

    /**
     * @return string
     */
    public function getMandateCreditorName(): string
    {
        return $this->mandateCreditorName;
    }

    /**
     * @param string $mandateCreditorName
     */
    public function setMandateCreditorName(string $mandateCreditorName): void
    {
        $this->mandateCreditorName = $mandateCreditorName;
    }

    /**
     * @return string
     */
    public function getMandateCreditorId(): string
    {
        return $this->mandateCreditorId;
    }

    /**
     * @param string $mandateCreditorId
     */
    public function setMandateCreditorId(string $mandateCreditorId): void
    {
        $this->mandateCreditorId = $mandateCreditorId;
    }

    /**
     * @return string
     */
    public function getMandateCreditorAddressFirst(): string
    {
        return $this->mandateCreditorAddressFirst;
    }

    /**
     * @param string $mandateCreditorAddressFirst
     */
    public function setMandateCreditorAddressFirst(string $mandateCreditorAddressFirst): void
    {
        $this->mandateCreditorAddressFirst = $mandateCreditorAddressFirst;
    }

    /**
     * @return string
     */
    public function getMandateCreditorAddressSecond(): string
    {
        return $this->mandateCreditorAddressSecond;
    }

    /**
     * @param string $mandateCreditorAddressSecond
     */
    public function setMandateCreditorAddressSecond(string $mandateCreditorAddressSecond): void
    {
        $this->mandateCreditorAddressSecond = $mandateCreditorAddressSecond;
    }

    /**
     * @return string
     */
    public function getMandateCreditorCountry(): string
    {
        return $this->mandateCreditorCountry;
    }

    /**
     * @param string $mandateCreditorCountry
     */
    public function setMandateCreditorCountry(string $mandateCreditorCountry): void
    {
        $this->mandateCreditorCountry = $mandateCreditorCountry;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}