<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

interface PaymentMethodInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function getAdditionalDescription(): string;

    public function getAction(): string;

    public function getPosition(): int;

    public function isActive(): bool;
}
