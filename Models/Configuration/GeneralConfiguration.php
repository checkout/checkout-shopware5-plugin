<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Models\Configuration;

use Doctrine\ORM\Mapping as ORM;
use Shopware\Components\Model\ModelEntity;

/**
 * @ORM\Entity()
 * @ORM\Table(name="s_plugin_cko_general_configuration")
 */
class GeneralConfiguration extends ModelEntity
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
     * @ORM\Column(name="sandbox_mode_enabled", type="boolean", nullable=false)
     */
    private $sandboxModeEnabled;

    /**
     * @var string
     *
     * @ORM\Column(name="private_key", type="string", nullable=false)
     */
    private $privateKey;

    /**
     * @var string
     *
     * @ORM\Column(name="public_key", type="string", nullable=false)
     */
    private $publicKey;

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
    public function isSandboxModeEnabled(): bool
    {
        return $this->sandboxModeEnabled;
    }

    /**
     * @param bool $sandboxModeEnabled
     */
    public function setSandboxModeEnabled(bool $sandboxModeEnabled): void
    {
        $this->sandboxModeEnabled = $sandboxModeEnabled;
    }

    /**
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey
     */
    public function setPrivateKey(string $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * @param string $publicKey
     */
    public function setPublicKey(string $publicKey): void
    {
        $this->publicKey = $publicKey;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}