<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\ApplePay;

interface MerchantValidationServiceInterface
{
    public function validateMerchant(string $url, int $shopId): array;
}