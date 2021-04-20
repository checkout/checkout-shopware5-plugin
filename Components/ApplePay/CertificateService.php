<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\ApplePay;

use CkoCheckoutPayment\Components\ApplePay\Exception\CertificateException;
use CkoCheckoutPayment\Components\ApplePay\Exception\OpenSSLNotAvailableException;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\ApplePayPaymentMethod;
use CkoCheckoutPayment\Models\Configuration\ApplePayConfiguration;

class CertificateService implements CertificateServiceInterface
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var LoggerServiceInterface
     */
    private $loggerService;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        LoggerServiceInterface $loggerService
    ) {
        $this->configurationService = $configurationService;
        $this->loggerService = $loggerService;
    }

    public function generateCsrCertificate(int $shopId): string
    {
        if (!extension_loaded('openssl')) {
            throw new OpenSSLNotAvailableException();
        }

        $this->validateConfigurationFields($shopId);

        $keyResource = openssl_pkey_new([
            'digest_alg' => 'sha512',
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
            'private_key_bits' => 2048
        ]);

        if (!$keyResource) {
            throw new CertificateException('unable to generate private key please check your configuration.');
        }

        $csrData = $this->getCsrData($shopId);

        $csrResource = openssl_csr_new($csrData, $keyResource, [
            'digest_alg' => 'sha256'
        ]);

        if (!$csrResource) {
            throw new CertificateException('unable to generate csr please check your configuration.');
        }

        if (!openssl_csr_export($csrResource, $csrString)) {
            throw new CertificateException('unable to export csr please check your configuration.');
        }

        $privateKey = $this->exportPrivateKey($keyResource, $shopId);

        try {
            $this->configurationService->updateConfiguration(ApplePayPaymentMethod::NAME, [
                ApplePayConfiguration::CSR => $csrString,
                ApplePayConfiguration::PRIVATE_KEY => $privateKey
            ], $shopId);
        } catch (\RuntimeException $exception) {
            throw new CertificateException('unable to update configuration please check your configuration.', $exception->getCode(), $exception);
        }

        return $csrString;
    }

    public function generatePemCertificate(string $csrCertificate, int $shopId): string
    {
        $pemCertificate = $this->convertCsrToPem($csrCertificate);

        try {
            $this->configurationService->updateConfiguration(ApplePayPaymentMethod::NAME, [
                ApplePayConfiguration::PEM => $pemCertificate
            ], $shopId);
        } catch (\RuntimeException $exception) {
            throw new CertificateException('unable to create pem certificate please check your configuration.', $exception->getCode(), $exception);
        }

        return $pemCertificate;
    }

    private function getCsrData(int $shopId): array
    {
        try {
            /** @var ApplePayConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(ApplePayPaymentMethod::NAME, $shopId, false);

            return [
                'commonName' => $configuration->getCsrCommonName(),
                'organizationName' => $configuration->getCsrOrganizationName(),
                'organizationalUnitName' => $configuration->getCsrOrganizationUnitName(),
                'localityName' => $configuration->getCsrLocalityName(),
                'stateOrProvinceName' => $configuration->getCsrStateOrProvinceName(),
                'countryName' => $configuration->getCsrCountryName(),
                'emailAddress' => $configuration->getCsrEmailAddress()
            ];
        } catch (\RuntimeException $exception) {
            throw new CertificateException('unable to generate csr please check your configuration.', $exception->getCode(), $exception);
        }
    }

    private function exportPrivateKey($keyResource, int $shopId): string
    {
        try {
            /** @var ApplePayConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(ApplePayPaymentMethod::NAME, $shopId, false);
            $csrCertificatePassword = $configuration->getCsrCertificatePassword();

            if (!empty($csrCertificatePassword)) {
                if (!openssl_pkey_export($keyResource, $privateKeyString, $csrCertificatePassword)) {
                    throw new CertificateException('unable to export csr private key with password please check your configuration.');
                }
            } else {
                if (!openssl_pkey_export($keyResource, $privateKeyString)) {
                    throw new CertificateException('unable to export csr private key please check your configuration.');
                }
            }
        } catch (\RuntimeException $exception) {
            throw new CertificateException('unable to export private key please check your configuration.', $exception->getCode(), $exception);
        }

        return $privateKeyString;
    }

    private function validateConfigurationFields(int $shopId): void
    {
        $requiredConfigurationFields = $this->getCsrData($shopId);

        foreach ($requiredConfigurationFields as $configurationName => $configurationValue) {
            if (empty($configurationValue)) {
                throw new CertificateException(
                    sprintf(
                        'configuration %s is empty for shop id %d',
                        $configurationName,
                        $shopId
                    )
                );
            }
        }
    }

    private function convertCsrToPem(string $csr): string
    {
        return '-----BEGIN CERTIFICATE-----' . PHP_EOL
            . chunk_split(base64_encode($csr), 64, PHP_EOL)
            . '-----END CERTIFICATE-----' . PHP_EOL;
    }
}
