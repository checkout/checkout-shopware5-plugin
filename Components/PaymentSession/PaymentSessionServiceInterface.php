<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentSession;

interface PaymentSessionServiceInterface
{
    public function set(string $key, string $value): void;

    public function setPaymentReference(string $reference): void;

    public function setMandateReference(string $mandateReference): void;

    public function setPaymentFailed(bool $hasFailed = true): void;

    public function setResponseCode(string $code): void;

    public function get(string $key): string;

    public function clearPaymentSession(): void;

    public function hasPaymentFailed(): bool;

    public function getResponseCode(): string;

    public function getPaymentReference(): string;

    public function getMandateReference(): ?string;

    public function getOrderVariables(): \ArrayObject;
}
