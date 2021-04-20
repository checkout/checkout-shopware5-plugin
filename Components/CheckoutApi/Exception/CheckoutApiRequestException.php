<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Exception;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Library\Exceptions\CheckoutHttpException;

class CheckoutApiRequestException extends \Exception
{
    private const EXCEPTION_MESSAGE = 'Checkout.com API Request has failed for the reason: %s.';

    /**
     * @var CheckoutException
     */
    private $checkoutException;

    public function __construct($message, $code, CheckoutException $checkoutException)
    {
        $this->checkoutException = $checkoutException;

        parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $message), $code, $checkoutException);
    }

    public function getContext(): array
    {
        if (!$this->checkoutException instanceof CheckoutHttpException) {
            return [
                'body' => [],
                'errors' => [],
                'message' => $this->checkoutException->getMessage(),
                'trace' => $this->checkoutException->getTraceAsString(),
            ];
        }

        return [
            'body' => json_decode($this->checkoutException->getBody(), true),
            'errors' => $this->checkoutException->getErrors(),
            'message' => $this->checkoutException->getMessage(),
            'trace' => $this->checkoutException->getTraceAsString(),
        ];
    }

    public function getMainErrorReason(): ?string
    {
        return $this->getContext()['errors'][0] ?: null;
    }
}
