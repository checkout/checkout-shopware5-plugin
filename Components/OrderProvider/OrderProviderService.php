<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\OrderProvider;

use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Repository as OrderRepository;
use Shopware\Models\Order\Status as OrderStatus;

class OrderProviderService implements OrderProviderServiceInterface
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var OrderRepository
     */
    private $orderStatusRepository;

    public function __construct(ModelManager $modelManager)
    {
        $this->orderRepository = $modelManager->getRepository(Order::class);
        $this->orderStatusRepository = $modelManager->getRepository(OrderStatus::class);
    }

    public function getOrderById(int $orderId): Order
    {
        /** @var Order $order */
        $order = $this->orderRepository->find($orderId);
        if ($order === null) {
            throw new \RuntimeException(sprintf('Order with id %d not found.', $orderId));
        }

        return $order;
    }

    public function getOrderByNumber(string $orderNumber): Order
    {
        /** @var Order $order */
        $order = $this->orderRepository->findOneBy(['number' => $orderNumber]);
        if ($order === null) {
            throw new \RuntimeException(sprintf('Order with number %s not found.', $orderNumber));
        }

        return $order;
    }

    public function getOrderStatusById(int $orderStatusId): ?string
    {
        /** @var OrderStatus $orderStatus */
        $orderStatus = $this->orderStatusRepository->find($orderStatusId);
        if ($orderStatus === null) {
            return null;
        }

        return $orderStatus->getName();
    }
}
