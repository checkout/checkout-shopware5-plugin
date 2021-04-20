<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class PayPalPaymentMethod implements PaymentMethodInterface
{
    public const NAME = 'cko_paypal';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'PayPal';
    }

    public function getAdditionalDescription(): string
    {
        return 'PayPal payment';
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
