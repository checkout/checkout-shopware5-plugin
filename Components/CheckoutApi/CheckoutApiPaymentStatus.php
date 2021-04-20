<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi;

final class CheckoutApiPaymentStatus
{
    public const WEBHOOK_PAYMENT_PENDING = 'payment_pending';
    public const WEBHOOK_PAYMENT_APPROVED = 'payment_approved';
    public const WEBHOOK_PAYMENT_DECLINED = 'payment_declined';
    public const WEBHOOK_PAYMENT_CAPTURED = 'payment_captured';
    public const WEBHOOK_PAYMENT_REFUNDED = 'payment_refunded';
    public const WEBHOOK_PAYMENT_VOID = 'payment_void';
    public const WEBHOOK_PAYMENT_EXPIRED = 'payment_expired';
    public const WEBHOOK_PAYMENT_CAPTURE_PENDING = 'payment_capture_pending';

    public const API_PAYMENT_PENDING = 'Pending';
    public const API_PAYMENT_APPROVED = 'Authorized';
    public const API_PAYMENT_DECLINED = 'Declined';

    public const API_PAYMENT_CAPTURED = 'Captured';
    public const API_PAYMENT_CAPTURED_PARTIALLY = 'Partially Captured';

    public const API_PAYMENT_REFUNDED = 'Refunded';
    public const API_PAYMENT_REFUNDED_PARTIALLY = 'Partially Refunded';

    public const API_PAYMENT_VOID = 'Voided';
    public const API_PAYMENT_EXPIRED = 'Payment expired';
    public const API_PAYMENT_CAPTURE_PENDING = 'Deferred capture';
}
