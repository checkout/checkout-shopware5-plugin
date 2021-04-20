<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class IdealPaymentMethod implements PaymentMethodInterface
{
    public const NAME = 'cko_ideal';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'iDEAL';
    }

    public function getAdditionalDescription(): string
    {
        return 'iDEAL payment';
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
