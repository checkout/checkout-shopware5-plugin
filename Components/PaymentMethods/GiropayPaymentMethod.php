<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class GiropayPaymentMethod implements PaymentMethodInterface
{
    public const NAME = 'cko_giropay';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'Giropay';
    }

    public function getAdditionalDescription(): string
    {
        return 'Giropay payment';
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
