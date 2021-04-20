<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\ApplePay\Exception;

class OpenSSLNotAvailableException extends CertificateException
{
    private const MESSAGE = 'OpenSSL is not available on this system.';

    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}