<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs;

class ApplePayStruct
{
    /**
     * @var null|string
     */
    private $transactionId;

    /**
     * @var null|string
     */
    private $publicKeyHash;

    /**
     * @var null|string
     */
    private $ephemeralPublicKey;

    /**
     * @var null|string
     */
    private $version;

    /**
     * @var null|string
     */
    private $signature;

    /**
     * @var null|string
     */
    private $data;

    public function __construct(
        ?string $transactionId,
        ?string $publicKeyHash,
        ?string $ephemeralPublicKey,
        ?string $version,
        ?string $signature,
        ?string $data
    ) {
        $this->transactionId = $transactionId;
        $this->publicKeyHash = $publicKeyHash;
        $this->ephemeralPublicKey = $ephemeralPublicKey;
        $this->version = $version;
        $this->signature = $signature;
        $this->data = $data;
    }

    /**
     * @return string|null
     */
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    /**
     * @param string|null $transactionId
     */
    public function setTransactionId(?string $transactionId): void
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return string|null
     */
    public function getPublicKeyHash(): ?string
    {
        return $this->publicKeyHash;
    }

    /**
     * @param string|null $publicKeyHash
     */
    public function setPublicKeyHash(?string $publicKeyHash): void
    {
        $this->publicKeyHash = $publicKeyHash;
    }

    /**
     * @return string|null
     */
    public function getEphemeralPublicKey(): ?string
    {
        return $this->ephemeralPublicKey;
    }

    /**
     * @param string|null $ephemeralPublicKey
     */
    public function setEphemeralPublicKey(?string $ephemeralPublicKey): void
    {
        $this->ephemeralPublicKey = $ephemeralPublicKey;
    }

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string|null $version
     */
    public function setVersion(?string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return string|null
     */
    public function getSignature(): ?string
    {
        return $this->signature;
    }

    /**
     * @param string|null $signature
     */
    public function setSignature(?string $signature): void
    {
        $this->signature = $signature;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @param string|null $data
     */
    public function setData(?string $data): void
    {
        $this->data = $data;
    }
}