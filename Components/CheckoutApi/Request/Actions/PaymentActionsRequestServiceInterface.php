<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Actions;

use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentActionsResponseStruct;

interface PaymentActionsRequestServiceInterface
{
    public function getPaymentActions(string $paymentId, int $shopId): PaymentActionsResponseStruct;
}
