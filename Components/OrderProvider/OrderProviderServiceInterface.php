<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\OrderProvider;

use Shopware\Models\Order\Order;

interface OrderProviderServiceInterface
{
    public function getOrderById(int $orderId): Order;

    public function getOrderByNumber(string $orderNumber): Order;

    public function getOrderStatusById(int $orderStatusId): ?string;
}
