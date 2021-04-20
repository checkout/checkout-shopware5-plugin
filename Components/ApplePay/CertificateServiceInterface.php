<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\ApplePay;

interface CertificateServiceInterface
{
    public function generateCsrCertificate(int $shopId): string;

    public function generatePemCertificate(string $csrCertificate, int $shopId): string;
}
