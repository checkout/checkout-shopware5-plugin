<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Models\Configuration;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_plugin_cko_sofort_configuration")
 */
class SofortConfiguration extends ModelEntity
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
     * @var int
     *
     * @ORM\Column(name="payment_status_auth_id", type="integer", nullable=false)
     */
    private $paymentStatusAuthId;

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
     * @return int
     */
    public function getPaymentStatusAuthId(): int
    {
        return $this->paymentStatusAuthId;
    }

    /**
     * @param int $paymentStatusAuthId
     */
    public function setPaymentStatusAuthId(int $paymentStatusAuthId): void
    {
        $this->paymentStatusAuthId = $paymentStatusAuthId;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}