<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentStatusMapper;

use CkoCheckoutPayment\Components\CheckoutApi\CheckoutApiPaymentStatus;
use Shopware\Models\Order\Status as OrderStatus;

class PaymentStatusMapperService implements PaymentStatusMapperServiceInterface
{
    private const MAPPED_SHOPWARE_STATUS = [
        CheckoutApiPaymentStatus::API_PAYMENT_PENDING => OrderStatus::PAYMENT_STATE_OPEN,
        CheckoutApiPaymentStatus::API_PAYMENT_APPROVED => OrderStatus::PAYMENT_STATE_THE_CREDIT_HAS_BEEN_ACCEPTED,
        CheckoutApiPaymentStatus::API_PAYMENT_DECLINED => OrderStatus::PAYMENT_STATE_NO_CREDIT_APPROVED,
        CheckoutApiPaymentStatus::API_PAYMENT_CAPTURED => OrderStatus::PAYMENT_STATE_COMPLETELY_PAID,
        CheckoutApiPaymentStatus::API_PAYMENT_CAPTURED_PARTIALLY => OrderStatus::PAYMENT_STATE_PARTIALLY_PAID,
        CheckoutApiPaymentStatus::API_PAYMENT_REFUNDED => OrderStatus::PAYMENT_STATE_RE_CREDITING,
        CheckoutApiPaymentStatus::API_PAYMENT_REFUNDED_PARTIALLY => OrderStatus::PAYMENT_STATE_RE_CREDITING,
        CheckoutApiPaymentStatus::API_PAYMENT_VOID => OrderStatus::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED,
        CheckoutApiPaymentStatus::API_PAYMENT_EXPIRED => OrderStatus::PAYMENT_STATE_DELAYED,
        CheckoutApiPaymentStatus::API_PAYMENT_CAPTURE_PENDING => OrderStatus::PAYMENT_STATE_OPEN,
    ];

    public function mapStatus(string $originalApiStatus): int
    {
        return static::MAPPED_SHOPWARE_STATUS[$originalApiStatus] ?? OrderStatus::PAYMENT_STATE_REVIEW_NECESSARY;
    }
}
