<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\ApplePay;

use CkoCheckoutPayment\Components\ApplePay\Exception\MerchantValidationFailedException;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\ApplePayPaymentMethod;
use CkoCheckoutPayment\Models\Configuration\ApplePayConfiguration;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class MerchantValidationService implements MerchantValidationServiceInterface
{
    /**
     * @var ConfigurationServiceInterface
     */
    private $configurationService;

    /**
     * @var DependencyProviderServiceInterface
     */
    private $dependencyProviderService;

    public function __construct(
        ConfigurationServiceInterface $configurationService,
        DependencyProviderServiceInterface $dependencyProviderService
    ) {
        $this->configurationService = $configurationService;
        $this->dependencyProviderService = $dependencyProviderService;
    }

    public function validateMerchant(string $url, int $shopId): array
    {
        try {
            /** @var ApplePayConfiguration $configuration */
            $configuration = $this->configurationService->getPaymentMethodConfiguration(ApplePayPaymentMethod::NAME, $shopId);
            $pemCertificate = trim($configuration->getPem());

            if (!$pemCertificate) {
                throw new MerchantValidationFailedException('apple pay pem certificate is empty');
            }

            $pemCertificateTmpFileResource = $this->createTmpFileResource($pemCertificate);
            $pemCertificateTmpMetaData = stream_get_meta_data($pemCertificateTmpFileResource);

            if (!$pemCertificateTmpMetaData) {
                throw new MerchantValidationFailedException('unable to get apple pay pem certificate tmp file meta data');
            }

            $privateKey = trim($configuration->getPrivateKey());

            if (!$privateKey) {
                throw new MerchantValidationFailedException('apple pay private key is empty');
            }

            $privateKeyTmpFileResource = $this->createTmpFileResource($privateKey);
            $privateKeyTmpMetaData = stream_get_meta_data($privateKeyTmpFileResource);

            if (!$privateKeyTmpMetaData) {
                throw new MerchantValidationFailedException('unable to get apple pay private key tmp file meta data');
            }

            $client = new Client();

            $sslKeyOptions = $privateKeyTmpMetaData['uri'];
            $csrCertificatePassword = $configuration->getCsrCertificatePassword();

            if (!empty($csrCertificatePassword)) {
                $sslKeyOptions = [$privateKeyTmpMetaData['uri'], $csrCertificatePassword];
            }

            $shop = $this->dependencyProviderService->getShop();

            $response = $client->post($url, [
                'cert' => $pemCertificateTmpMetaData['uri'],
                'ssl_key' => $sslKeyOptions,
                'exceptions' => false,
                'json' => [
                    'merchantIdentifier' => $configuration->getMerchantId(),
                    'displayName' => $shop->getName(),
                    'initiative' => 'web',
                    'initiativeContext' => $configuration->getCsrCommonName()
                ]
            ]);

            $this->deleteTmpFileResource($pemCertificateTmpFileResource);
            $this->deleteTmpFileResource($privateKeyTmpFileResource);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $guzzleException) {
            throw new MerchantValidationFailedException(
                sprintf(
                    'sending request failed with message %s and code %s',
                    $guzzleException->getMessage(),
                    $guzzleException->getCode()
                ),
                $guzzleException->getCode(),
                $guzzleException
            );
        } catch (\RuntimeException $runtimeException) {
            throw new MerchantValidationFailedException(
                sprintf(
                    'configuration is missing %s',
                    $runtimeException->getMessage()
                ),
                $runtimeException->getCode(),
                $runtimeException
            );
        }
    }

    private function createTmpFileResource(string $content)
    {
        $createdTmpFile = tmpfile();
        if (!$createdTmpFile) {
            throw new MerchantValidationFailedException('unable to create apple pay tmp file');
        }

        if (!fwrite($createdTmpFile, $content)) {
            throw new MerchantValidationFailedException('unable to write to apple pay tmp file');
        }

        return $createdTmpFile;
    }

    private function deleteTmpFileResource($tmpFileResource): void
    {
        $tmpFileMetaData = stream_get_meta_data($tmpFileResource);

        if (!$tmpFileMetaData) {
            throw new MerchantValidationFailedException('unable to get tmp file meta data while trying to delete tmp file');
        }

        $tmpFile = $tmpFileMetaData['uri'];

        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }
    }
}