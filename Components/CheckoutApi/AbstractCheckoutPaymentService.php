<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi;

use Checkout\CheckoutApi;
use Checkout\Models\Address;
use Checkout\Models\Payments\Customer;
use Checkout\Models\Payments\Payment;
use Checkout\Models\Payments\Shipping;
use Checkout\Models\Payments\Source;
use CkoCheckoutPayment\Components\CheckoutApi\ApiClient\CheckoutApiClientServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\Configuration\ConfigurationServiceInterface;
use CkoCheckoutPayment\Components\DependencyProvider\DependencyProviderServiceInterface;
use CkoCheckoutPayment\Components\Logger\LoggerServiceInterface;
use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceFactory;
use Doctrine\Common\Persistence\ObjectRepository;
use Shopware\Models\Country\Country;

abstract class AbstractCheckoutPaymentService
{
    protected const CALCULATE_TYPE_MULTIPLE = 'TYPE_MULTIPLE';
    protected const CALCULATE_TYPE_DIVIDE = 'TYPE_DIVIDE';

    /**
     * @var CheckoutApiClientServiceInterface
     */
    protected $apiClientService;

    /**
     * @var ConfigurationServiceInterface
     */
    protected $configurationService;

    /**
     * @var DependencyProviderServiceInterface
     */
    protected $dependencyProviderService;

    /**
     * @var PaymentSessionServiceFactory
     */
    protected $paymentSessionServiceFactory;

    /**
     * @var LoggerServiceInterface
     */
    protected $loggerService;

    /**
     * @var ObjectRepository
     */
    protected $countryRepository;

    public function __construct(
        CheckoutApiClientServiceInterface $apiClientService,
        ConfigurationServiceInterface $configurationService,
        DependencyProviderServiceInterface $dependencyProviderService,
        PaymentSessionServiceFactory $paymentSessionServiceFactory,
        LoggerServiceInterface $loggerService,
        ObjectRepository $countryRepository
    ) {
        $this->apiClientService = $apiClientService;
        $this->configurationService = $configurationService;
        $this->dependencyProviderService = $dependencyProviderService;
        $this->paymentSessionServiceFactory = $paymentSessionServiceFactory;
        $this->loggerService = $loggerService;
        $this->countryRepository = $countryRepository;
    }

    protected function calculateAmount(float $amount, string $type = self::CALCULATE_TYPE_MULTIPLE)
    {
        if ($type === self::CALCULATE_TYPE_DIVIDE) {
            return $amount / 100;
        }

        return $amount * 100;
    }

    protected function getCountryById(int $countryId): Country
    {
        /** @var Country $country */
        $country = $this->countryRepository->find($countryId);
        if ($country === null) {
            throw new \RuntimeException(sprintf('Country with id %d was not found.', $countryId));
        }

        return $country;
    }

    protected function getShopId(): int
    {
        return $this->dependencyProviderService->getShop()->getId();
    }

    protected function createApiClient(): CheckoutApi
    {
        $shopId = $this->getShopId();

        return $this->apiClientService->createClient($shopId);
    }

    protected function createCreateCustomerData(array $user): Customer
    {
        $billingAddress = $user['billingaddress'];

        $customer = new Customer();
        $customer->email = $user['additional']['user']['email'];
        $customer->name = sprintf('%s %s', $billingAddress['firstname'], $billingAddress['lastname']);

        return $customer;
    }

    protected function createShippingAddress(array $shippingAddressData): Shipping
    {
        return new Shipping($this->createAddress($shippingAddressData));
    }

    protected function createBillingAddress(array $billingAddressData): Address
    {
        return $this->createAddress($billingAddressData);
    }

    protected function createPaymentRequestFromStruct(Source $paymentSource, PaymentRequestStruct $paymentRequestStruct): Payment
    {
        $payment = new Payment($paymentSource, $paymentRequestStruct->getCurrency());

        $payment->capture = $paymentRequestStruct->isAutoCaptureEnabled();
        $payment->reference = $paymentRequestStruct->getReference();
        $payment->amount = $this->calculateAmount($paymentRequestStruct->getAmount());
        $payment->success_url = $paymentRequestStruct->getSuccessUrl();
        $payment->failure_url = $paymentRequestStruct->getFailureUrl();
        $payment->metadata = array_merge((array)$payment->getValue('metadata'), $this->createMetaData());

        $user = $paymentRequestStruct->getUser();
        $payment->customer = $this->createCreateCustomerData($user);
        $payment->shipping = $this->createShippingAddress($user['shippingaddress']);

        return $payment;
    }

    private function createAddress(array $addressData): Address
    {
        $countryId = (int)$addressData['countryID'];

        try {
            $country = $this->getCountryById($countryId);

            $address = new Address();
            $address->address_line1 = $addressData['street'];
            $address->address_line2 = $addressData['additionalAddressLine1'];
            $address->city = $addressData['city'];
            $address->state = $addressData['state'];
            $address->zip = $addressData['zipcode'];
            $address->country = $country->getIso();

            return $address;
        } catch (\RuntimeException $e) {
            throw new RequiredPaymentDetailsMissingException(
                sprintf(
                    'country id %d for address not found',
                    $countryId
                )
            );
        }
    }

    private function createMetaData(): array
    {
        return [
            'udf5' => json_encode([
                'server_url' => $this->dependencyProviderService->getShopUrl(),
                'sdk_data' => sprintf('PHP Version %s, Library Version %s', phpversion(), CheckoutApi::VERSION),
                'integration_data' => sprintf('Checkout.com Shopware 5 Plugin %s', $this->dependencyProviderService->getPluginVersion()),
                'platform_data' => sprintf('Shopware Version %s', $this->dependencyProviderService->getShopwareVersion())
            ])
        ];
    }
}
