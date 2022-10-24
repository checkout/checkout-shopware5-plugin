<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks;

use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceInterface;

class PaymentSessionServiceMock implements PaymentSessionServiceInterface
{
    private $data = [];

    public function set(string $key, string $value): void
    {
        $this->data[$key] = $value;
    }

    public function setPaymentReference(string $reference): void
    {
        // TODO: Implement setPaymentReference() method.
    }

    public function setMandateReference(string $mandateReference): void
    {
        // TODO: Implement setMandateReference() method.
    }

    public function setPaymentFailed(bool $hasFailed = true): void
    {
        // TODO: Implement setPaymentFailed() method.
    }

    public function setResponseCode(string $code): void
    {
        // TODO: Implement setResponseCode() method.
    }

    public function get(string $key): string
    {
        return $this->data[$key];
    }

    public function clearPaymentSession(): void
    {
        // TODO: Implement clearPaymentSession() method.
    }

    public function hasPaymentFailed(): bool
    {
        // TODO: Implement hasPaymentFailed() method.
    }

    public function getResponseCode(): string
    {
        // TODO: Implement getResponseCode() method.
    }

    public function getPaymentReference(): string
    {
        // TODO: Implement getPaymentReference() method.
    }

    public function getMandateReference(): ?string
    {
        // TODO: Implement getMandateReference() method.
    }

    public function getOrderVariables(): \ArrayObject
    {
        // TODO: Implement getOrderVariables() method.
    }
}