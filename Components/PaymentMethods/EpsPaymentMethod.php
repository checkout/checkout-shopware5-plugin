<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class EpsPaymentMethod implements PaymentMethodInterface
{
    public const NAME = 'cko_eps';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'eps';
    }

    public function getAdditionalDescription(): string
    {
        return 'eps payment';
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
