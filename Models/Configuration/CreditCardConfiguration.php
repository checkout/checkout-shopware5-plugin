<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Models\Configuration;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_plugin_cko_credit_card_configuration")
 */
class CreditCardConfiguration extends ModelEntity
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
     * @var bool
     *
     * @ORM\Column(name="auto_capture_enabled", type="boolean")
     */
    private $autoCaptureEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="three_ds_enabled", type="boolean")
     */
    private $threeDsEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="n3d_attempt_enabled", type="boolean")
     */
    private $n3dAttemptEnabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="dynamic_billing_descriptor_enabled", type="boolean")
     */
    private $dynamicBillingDescriptorEnabled;

    /**
     * @var null|string
     *
     * @ORM\Column(name="dynamic_billing_descriptor_name", type="string")
     */
    private $dynamicBillingDescriptorName;

    /**
     * @var null|string
     *
     * @ORM\Column(name="dynamic_billing_descriptor_city", type="string")
     */
    private $dynamicBillingDescriptorCity;

    /**
     * @var bool
     *
     * @ORM\Column(name="save_card_option_enabled", type="boolean")
     */
    private $saveCardOptionEnabled;

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
     * @return bool
     */
    public function isThreeDsEnabled(): bool
    {
        return $this->threeDsEnabled;
    }

    /**
     * @param bool $threeDsEnabled
     */
    public function setThreeDsEnabled(bool $threeDsEnabled): void
    {
        $this->threeDsEnabled = $threeDsEnabled;
    }

    /**
     * @return bool
     */
    public function isN3dAttemptEnabled(): bool
    {
        return $this->n3dAttemptEnabled;
    }

    /**
     * @param bool $n3dAttemptEnabled
     */
    public function setN3dAttemptEnabled(bool $n3dAttemptEnabled): void
    {
        $this->n3dAttemptEnabled = $n3dAttemptEnabled;
    }

    /**
     * @return bool
     */
    public function isDynamicBillingDescriptorEnabled(): bool
    {
        return $this->dynamicBillingDescriptorEnabled;
    }

    /**
     * @param bool $dynamicBillingDescriptorEnabled
     */
    public function setDynamicBillingDescriptorEnabled(bool $dynamicBillingDescriptorEnabled): void
    {
        $this->dynamicBillingDescriptorEnabled = $dynamicBillingDescriptorEnabled;
    }

    /**
     * @return string|null
     */
    public function getDynamicBillingDescriptorName(): ?string
    {
        return $this->dynamicBillingDescriptorName;
    }

    /**
     * @param string|null $dynamicBillingDescriptorName
     */
    public function setDynamicBillingDescriptorName(?string $dynamicBillingDescriptorName): void
    {
        $this->dynamicBillingDescriptorName = $dynamicBillingDescriptorName;
    }

    /**
     * @return string|null
     */
    public function getDynamicBillingDescriptorCity(): ?string
    {
        return $this->dynamicBillingDescriptorCity;
    }

    /**
     * @param string|null $dynamicBillingDescriptorCity
     */
    public function setDynamicBillingDescriptorCity(?string $dynamicBillingDescriptorCity): void
    {
        $this->dynamicBillingDescriptorCity = $dynamicBillingDescriptorCity;
    }

    /**
     * @return bool
     */
    public function isSaveCardOptionEnabled(): bool
    {
        return $this->saveCardOptionEnabled;
    }

    /**
     * @param bool $saveCardOptionEnabled
     */
    public function setSaveCardOptionEnabled(bool $saveCardOptionEnabled): void
    {
        $this->saveCardOptionEnabled = $saveCardOptionEnabled;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}