<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class BancontactPaymentMethod implements PaymentMethodInterface
{
    public const NAME = 'cko_bancontact';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDescription(): string
    {
        return 'Bancontact';
    }

    public function getAdditionalDescription(): string
    {
        return 'Bancontact payment';
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
