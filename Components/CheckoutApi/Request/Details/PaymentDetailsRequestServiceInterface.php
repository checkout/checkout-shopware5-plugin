<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Details;

use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentDetailsResponseStruct;

interface PaymentDetailsRequestServiceInterface
{
    /**
     * @throws CheckoutApiRequestException
     */
    public function getPaymentDetails(string $threeDsSessionId, ?int $shopId): PaymentDetailsResponseStruct;
}
