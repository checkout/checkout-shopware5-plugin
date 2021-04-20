<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs;

class VoidRequestStruct
{
    /**
     * @var string
     */
    private $paymentId;

    /**
     * @var string
     */
    private $paymentMethodName;

    /**
     * @var int
     */
    private $shopId;

    public function __construct(
        string $paymentId,
        string $paymentMethodName,
        int $shopId
    ) {
        $this->paymentId = $paymentId;
        $this->paymentMethodName = $paymentMethodName;
        $this->shopId = $shopId;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
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
}
