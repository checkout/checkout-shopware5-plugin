<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethodValidator;

use CkoCheckoutPayment\Components\CheckoutApi\Request\PaymentRequestServiceInterface;

interface PaymentMethodValidatorServiceInterface
{
    public function isCheckoutPaymentMethod(string $paymentMethodName): bool;

    public function isPaymentMethodValid(string $paymentMethodName): bool;

    public function addPaymentRequestService(PaymentRequestServiceInterface $paymentRequestService): void;
}