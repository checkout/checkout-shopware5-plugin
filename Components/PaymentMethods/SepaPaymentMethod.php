<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class SepaPaymentMethod implements PaymentMethodInterface
{
    public const NAME = 'cko_sepa';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'SEPA';
    }

    public function getAdditionalDescription(): string
    {
        return 'SEPA payment';
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
