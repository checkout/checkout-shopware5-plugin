<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\ApplePay\Exception;

class CertificateException extends \RuntimeException
{
    private const MESSAGE = 'Apple Pay Certificate Failed: %s';

    public function __construct($message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $message), $code, $previous);
    }
}