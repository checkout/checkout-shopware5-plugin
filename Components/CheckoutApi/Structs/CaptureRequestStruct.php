<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs;

class CaptureRequestStruct
{
    /**
     * @var string
     */
    private $paymentId;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $paymentMethodName;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var float
     */
    private $captureAmount;

    /**
     * @var bool
     */
    private $isPartialCapture;

    public function __construct(
        string $paymentId,
        string $reference,
        string $paymentMethodName,
        int $shopId,
        float $captureAmount,
        bool $isPartialCapture
    ) {
        $this->paymentId = $paymentId;
        $this->reference = $reference;
        $this->paymentMethodName = $paymentMethodName;
        $this->shopId = $shopId;
        $this->captureAmount = $captureAmount;
        $this->isPartialCapture = $isPartialCapture;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getPaymentMethodName(): string
    {
        return $this->paymentMethodName;
    }

    public function setPaymentMethodName(string $paymentMethodName): void
    {
        $this->paymentMethodName = $paymentMethodName;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): void
    {
        $this->shopId = $shopId;
    }

    public function getCaptureAmount(): float
    {
        return $this->captureAmount;
    }

    public function setCaptureAmount(float $captureAmount): void
    {
        $this->captureAmount = $captureAmount;
    }

    public function isPartialCapture(): bool
    {
        return $this->isPartialCapture;
    }

    public function setIsPartialCapture(bool $isPartialCapture): void
    {
        $this->isPartialCapture = $isPartialCapture;
    }
}
