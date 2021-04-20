<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\Payment;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Builder\RequestBuilder\KlarnaRequestBuilderServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\ApiClient\CheckoutApiClientServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Components\PaymentMethods\KlarnaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceFactory;
use CkoCheckoutPayment\Components\RequestConstants;
use Doctrine\Common\Persistence\ObjectRepository;
use Shopware\Models\Country\Country;

class KlarnaPaymentRequestService extends AbstractCheckoutPaymentService implements PaymentRequestServiceInterface
{
    /**
     * @var KlarnaRequestBuilderServiceInterface
     */
    private $klarnaRequestBuilderService;

    public function __construct(
        CheckoutApiClientServiceInterface $apiClientService,
        ConfigurationServiceInterface $configurationService,
        DependencyProviderServiceInterface $dependencyProviderService,
        PaymentSessionServiceFactory $paymentSessionServiceFactory,
        LoggerServiceInterface $loggerService,
        ObjectRepository $countryRepository,
        KlarnaRequestBuilderServiceInterface $klarnaRequestBuilderService
    ) {
        parent::__construct(
            $apiClientService,
            $configurationService,
            $dependencyProviderService,
            $paymentSessionServiceFactory,
            $loggerService,
            $countryRepository
        );

        $this->klarnaRequestBuilderService = $klarnaRequestBuilderService;
    }

    public function supportsPaymentRequest(string $paymentMethodName): bool
    {
        return $paymentMethodName === KlarnaPaymentMethod::NAME;
    }

    public function sendPaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        if (!$this->isPaymentRequestValid($paymentRequestStruct)) {
            throw new RequiredPaymentDetailsMissingException(KlarnaPaymentMethod::NAME);
        }

        $user = $paymentRequestStruct->getUser();
        $billingAddress = $user['billingaddress'];
        $country = $this->getBillingAddressCountry((int)$billingAddress['countryId']);

        $klarnaSource = $this->klarnaRequestBuilderService->createKlarnaSource(
            $paymentRequestStruct->getToken(),
            $country->getIso(),
            $billingAddress,
            $paymentRequestStruct->getBasket(),
            $user['additional']['user']
        );
        $klarna = $this->createPaymentRequestFromStruct($klarnaSource, $paymentRequestStruct);

        try {
            $client = $this->createApiClient();

            /** @var Payment $paymentRequest */
            $paymentRequest = $client->payments()->request($klarna);
            $paymentResponse = new PaymentResponseStruct($paymentRequest);

            $this->loggerService->info(sprintf('Processing klarna payment %s with status %s', $paymentResponse->getPaymentId(), $paymentResponse->getStatus()));

            return $paymentResponse;
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    public function isPaymentSessionValid(): bool
    {
        $paymentSessionService = $this->paymentSessionServiceFactory->createPaymentSessionService();

        $token = $paymentSessionService->get(RequestConstants::TOKEN);

        return !empty($token);
    }

    private function getBillingAddressCountry(int $countryId): Country
    {
        try {
            return $this->getCountryById($countryId);
        } catch (\RuntimeException $runtimeException) {
            throw new RequiredPaymentDetailsMissingException(
                sprintf(
                    'country id %d for klarna address not found',
                    $countryId
                )
            );
        }
    }

    private function isPaymentRequestValid(PaymentRequestStruct $paymentRequestStruct): bool
    {
        return !empty($paymentRequestStruct->getToken());
    }
}
