<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\Webhooks;

use CkoCheckoutPayment\Components\Logger\LoggerService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Order\Order;
use Shopware\Models\Order\Status;

class WebhooksService
{
    /**
     * @var LoggerService
     */
    private $loggerService;

    /**
     * @var ModelManager
     */
    private $modelManager;

    /**
     * @var EntityRepository
     */
    private $orderRepository;

    /**
     * @var EntityRepository
     */
    private $statusRepository;

    public function __construct(
        LoggerService $loggerService,
        ModelManager $modelManager
    ) {
        $this->loggerService = $loggerService;
        $this->modelManager = $modelManager;
        $this->orderRepository = $this->modelManager->getRepository(Order::class);
        $this->statusRepository = $this->modelManager->getRepository(Status::class);
    }

    public function handlePaymentApproved(string $paymentId): void
    {
        $this->setOrderPaymentStatus($paymentId, Status::PAYMENT_STATE_THE_CREDIT_HAS_BEEN_ACCEPTED);
    }

    public function handlePaymentCaptured(string $paymentId): void
    {
        /** @var Order $order */
        $order = $this->orderRepository->findOneBy(['transactionId' => $paymentId]);

        //TODO: Handle missing order
        if (!$order) {
            $this->loggerService->error("Unable to capture order for payment id {$paymentId} order was not found");

            throw new \Exception("Order {$paymentId} not found"); //TODO: properly handle order not found
        }

        $this->setOrderPaymentStatus($paymentId, Status::PAYMENT_STATE_COMPLETELY_PAID);
    }

    public function handlePaymentRefunded(string $paymentId): void
    {
        /** @var Order $order */
        $order = $this->orderRepository->findOneBy(['transactionId' => $paymentId]);

        //TODO: Handle missing order
        if (!$order) {
            $this->loggerService->error("Unable to refund order for payment id {$paymentId} order was not found");

            return;
        }

        $this->setOrderPaymentStatus($paymentId, Status::PAYMENT_STATE_RE_CREDITING);

        $this->loggerService->info("Order for PaymentID {$paymentId} set to refunded");
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    private function setOrderPaymentStatus(string $paymentId, int $paymentStatus): void
    {
        /** @var Order $order */
        $order = $this->orderRepository->findOneBy(['transactionId' => $paymentId]);

        if ($order) {
            /** @var Status $status */
            $status = $this->statusRepository->find($paymentStatus);

            $order->setPaymentStatus($status);
            $this->modelManager->flush();

            $this->loggerService->info("Order for PaymentID {$paymentId} set to approved");
        }
    }
}
