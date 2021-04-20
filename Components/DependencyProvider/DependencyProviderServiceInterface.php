<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\DependencyProvider;

use Shopware\Models\Shop\Shop;

interface DependencyProviderServiceInterface
{
    public function getShop(?int $shopId = null): Shop;

    public function getShopUrl(?int $shopId = null): string;

    public function getShopwareVersion(): string;

    public function getPluginVersion(): string;
}
