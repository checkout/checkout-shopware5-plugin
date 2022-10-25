<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class GooglePayPaymentMethod implements PaymentMethodInterface
{
    public const NAME = 'cko_googlepay';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'Google Pay';
    }

    public function getAdditionalDescription(): string
    {
        return 'Google Pay payment';
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
