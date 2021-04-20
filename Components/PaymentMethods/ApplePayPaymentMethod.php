<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class ApplePayPaymentMethod implements PaymentMethodInterface
{
    public const NAME = 'cko_applepay';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'Apple Pay';
    }

    public function getAdditionalDescription(): string
    {
        return 'Apple Pay payment';
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
