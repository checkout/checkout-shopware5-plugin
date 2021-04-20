<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs;

class RefundRequestStruct
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
     * @var int
     */
    private $shopId;

    /**
     * @var float
     */
    private $refundAmount;

    /**
     * @var bool
     */
    private $isPartialRefund;

    public function __construct(
        string $paymentId,
        string $reference,
        int $shopId,
        float $refundAmount,
        bool $isPartialRefund
    ) {
        $this->paymentId = $paymentId;
        $this->reference = $reference;
        $this->shopId = $shopId;
        $this->refundAmount = $refundAmount;
        $this->isPartialRefund = $isPartialRefund;
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

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): void
    {
        $this->shopId = $shopId;
    }

    public function getRefundAmount(): float
    {
        return $this->refundAmount;
    }

    public function setRefundAmount(float $refundAmount): void
    {
        $this->refundAmount = $refundAmount;
    }

    public function isPartialRefund(): bool
    {
        return $this->isPartialRefund;
    }

    public function setIsPartialRefund(bool $isPartialRefund): void
    {
        $this->isPartialRefund = $isPartialRefund;
    }
}
