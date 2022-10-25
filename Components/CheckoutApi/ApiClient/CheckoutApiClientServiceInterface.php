<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\ApiClient;

use Checkout\CheckoutApi;

interface CheckoutApiClientServiceInterface
{
    public function createClient(?int $shopId)/*: CheckoutApi*/;

    public function getPublicKey(?int $shopId): ?string;

    public function isSandboxMode(?int $shopId): bool;
}
