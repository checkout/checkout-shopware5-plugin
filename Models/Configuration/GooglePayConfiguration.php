<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Models\Configuration;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_plugin_cko_google_pay_configuration")
 */
class GooglePayConfiguration extends ModelEntity
{
    public const NETWORK_VISA = 'VISA';
    public const NETWORK_MASTERCARD = 'MASTERCARD';

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
     * @var bool
     *
     * @ORM\Column(name="auto_capture_enabled", type="boolean")
     */
    private $autoCaptureEnabled;

    /**
     * @var string
     *
     * @ORM\Column(name="merchant_id", type="string")
     */
    private $merchantId;

    /**
     * @var bool
     *
     * @ORM\Column(name="allowed_card_networks_visa_enabled", type="boolean")
     */
    private $allowedCardNetworksVisaEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="allowed_card_networks_mastercard_enabled", type="boolean")
     */
    private $allowedCardNetworksMastercardEnabled;

    /**
     * @var string
     *
     * @ORM\Column(name="button_color", type="string")
     */
    private $buttonColor;

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
     * @return bool
     */
    public function isAutoCaptureEnabled(): bool
    {
        return $this->autoCaptureEnabled;
    }

    /**
     * @param bool $autoCaptureEnabled
     */
    public function setAutoCaptureEnabled(bool $autoCaptureEnabled): void
    {
        $this->autoCaptureEnabled = $autoCaptureEnabled;
    }

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     */
    public function setMerchantId(string $merchantId): void
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @return bool
     */
    public function isAllowedCardNetworksVisaEnabled(): bool
    {
        return $this->allowedCardNetworksVisaEnabled;
    }

    /**
     * @param bool $allowedCardNetworksVisaEnabled
     */
    public function setAllowedCardNetworksVisaEnabled(bool $allowedCardNetworksVisaEnabled): void
    {
        $this->allowedCardNetworksVisaEnabled = $allowedCardNetworksVisaEnabled;
    }

    /**
     * @return bool
     */
    public function isAllowedCardNetworksMastercardEnabled(): bool
    {
        return $this->allowedCardNetworksMastercardEnabled;
    }

    /**
     * @param bool $allowedCardNetworksMastercardEnabled
     */
    public function setAllowedCardNetworksMastercardEnabled(bool $allowedCardNetworksMastercardEnabled): void
    {
        $this->allowedCardNetworksMastercardEnabled = $allowedCardNetworksMastercardEnabled;
    }

    /**
     * @return string
     */
    public function getButtonColor(): string
    {
        return $this->buttonColor;
    }

    /**
     * @param string $buttonColor
     */
    public function setButtonColor(string $buttonColor): void
    {
        $this->buttonColor = $buttonColor;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}