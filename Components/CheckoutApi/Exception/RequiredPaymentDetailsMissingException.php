<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Exception;

class RequiredPaymentDetailsMissingException extends \RuntimeException
{
    private const EXCEPTION_MESSAGE = 'Checkout.com request has failed for the reason: required payment details for %s missing.';

    public function __construct(string $message)
    {
        parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $message), 0);
    }
}
