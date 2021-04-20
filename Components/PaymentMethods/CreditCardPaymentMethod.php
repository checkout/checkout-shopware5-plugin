<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class CreditCardPaymentMethod implements PaymentMethodInterface
{
    public const NAME = 'cko_cc';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'Credit card';
    }

    public function getAdditionalDescription(): string
    {
        return 'Credit card payment';
    }

    public function getAction(): string
    {
        return 'CkoCheckoutPayment';
    }

    public function getPosition(): int
    {
        return 0;
    }

    public function isActive(): bool
    {
        return false;
    }
}
