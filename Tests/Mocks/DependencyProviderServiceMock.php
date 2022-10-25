<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks;

use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use Shopware\Models\Shop\Shop;

class DependencyProviderServiceMock implements DependencyProviderServiceInterface
{
    public function getShop(?int $shopId = null): Shop
    {
        return new ShopMock();
    }

    public function getShopUrl(?int $shopId = null): string
    {
        return 'https://127.0.0.1';
    }

    public function getShopwareVersion(): string
    {
        return '5.7.3';
    }

    public function getPluginVersion(): string
    {
        return '1.0.5';
    }
}