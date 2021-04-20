<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class SofortPaymentMethod implements PaymentMethodInterface
{
    public const NAME = 'cko_sofort';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'Sofort';
    }

    public function getAdditionalDescription(): string
    {
        return 'Sofort payment';
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
