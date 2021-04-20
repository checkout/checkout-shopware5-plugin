<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentStatusMapper;

interface PaymentStatusMapperServiceInterface
{
    public function mapStatus(string $originalApiStatus): int;
}
