<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Refund;

use CkoCheckoutPayment\Components\CheckoutApi\Structs\RefundRequestStruct;

interface RefundRequestServiceInterface
{
    public function refundPayment(RefundRequestStruct $refundRequestStruct): void;
}
