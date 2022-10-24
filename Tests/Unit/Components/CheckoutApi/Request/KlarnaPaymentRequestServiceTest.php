<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\CheckoutApi\Request;

use CkoCheckoutPayment\Components\CheckoutApi\Builder\RequestBuilder\KlarnaRequestBuilderService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Request\KlarnaPaymentRequestService;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\ApplePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\GooglePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\Logger\LoggerService;
use CkoCheckoutPayment\Components\PaymentMethods\KlarnaPaymentMethod;
use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Tests\Mocks\Api\Klarna\KlarnaPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\CheckoutApiClientServiceMock;
use CkoCheckoutPayment\Tests\Mocks\ConfigurationServiceMock;
use CkoCheckoutPayment\Tests\Mocks\CountryRepositoryMock;
use CkoCheckoutPayment\Tests\Mocks\DependencyProviderServiceMock;
use CkoCheckoutPayment\Tests\Mocks\PaymentSessionServiceFactoryMock;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Shopware\Components\DependencyInjection\Container;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Country\Country;

class KlarnaPaymentRequestServiceTest extends TestCase
{
    /**
     * @var PaymentSessionServiceFactoryMock
     */
    private $paymentSessionServiceFactoryMock;

    /**
     * @var CheckoutApiClientServiceMock
     */
    private $apiClientServiceMock;

    /**
     * @var KlarnaPaymentRequestService
     */
    private $klarnaPaymentRequestService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentSessionServiceFactoryMock = new PaymentSessionServiceFactoryMock($this->createMock(Container::class));
        $this->paymentSessionServiceFactoryMock->createPaymentSessionService();

        $this->apiClientServiceMock = new CheckoutApiClientServiceMock(KlarnaPaymentMethod::NAME);
        $dependencyProviderService = new DependencyProviderServiceMock();
        $loggerService = $this->createMock(LoggerService::class);
        $countryRepository = new CountryRepositoryMock($this->createMock(ModelManager::class), new ClassMetadata(Country::class));
        $this->klarnaPaymentRequestService = new KlarnaPaymentRequestService(
            $this->apiClientServiceMock,
            new ConfigurationServiceMock(),
            $dependencyProviderService,
            $this->paymentSessionServiceFactoryMock,
            $loggerService,
            $countryRepository,
            new KlarnaRequestBuilderService($dependencyProviderService, $this->apiClientServiceMock, $loggerService, $countryRepository)
        );
    }

    public function testSendPaymentRequest(): void
    {
        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentResponse = $this->klarnaPaymentRequestService->sendPaymentRequest($paymentRequestStruct);

        static::assertSame('EUR', $paymentResponse->getCurrency());
        static::assertSame('pay_abcabc', $paymentResponse->getPaymentId());
        static::assertSame('act_abcabc', $paymentResponse->getActionId());
        static::assertSame(500, $paymentResponse->getAmount());
        static::assertTrue($paymentResponse->getApproved());
        static::assertSame('Authorized', $paymentResponse->getStatus());
        static::assertSame('10000', $paymentResponse->getResponseCode());
        static::assertSame('Order Created', $paymentResponse->getResponseSummary());
        static::assertSame(['id' => 'cus_abcabc', 'email' => 'test@email.com', 'name' => 'firstname lastname'], $paymentResponse->getCustomer());
        static::assertSame('https://api.sandbox.checkout.com/payments/pay_abcabc', $paymentResponse->getRedirectionUrl());
        static::assertSame('2021-10-05T12:13:01Z', $paymentResponse->getProcessedOn());
        static::assertSame('testReference', $paymentResponse->getReference());
        static::assertSame(201, $paymentResponse->getHttpCode());

        static::assertInstanceOf(PaymentResponseStruct::class, $paymentResponse);
    }

    public function testSendPaymentRequestThrowsExceptionWhenApiExceptionIsThrown(): void
    {
        static::expectException(CheckoutApiRequestException::class);
        static::expectExceptionMessage('Checkout.com API Request has failed for the reason: '. KlarnaPaymentMock::class);

        $this->apiClientServiceMock->setShouldThrowApiException(true);

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $this->klarnaPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testSendPaymentRequestThrowsExceptionWhenRequiredPaymentDetailsMissing(): void
    {
        static::expectException(RequiredPaymentDetailsMissingException::class);
        static::expectExceptionMessage('Checkout.com request has failed for the reason: required payment details for cko_klarna missing.');

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentRequestStruct->setToken('');
        $this->klarnaPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testIsPaymentSessionValidWithToken(): void
    {
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::TOKEN, 'testToken');
        static::assertTrue($this->klarnaPaymentRequestService->isPaymentSessionValid());
    }

    private function getTestPaymentRequestStruct(): PaymentRequestStruct
    {
        return new PaymentRequestStruct(
            KlarnaPaymentMethod::NAME,
            false,
            5.00,
            'EUR',
            'testReference',
            'testPurpose',
            'testSuccessUrl',
            'testFailureUrl',
            'testBasketSignature',
            [
                'billingaddress' => [
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'countryId' => 1,
                    'street' => 'test 123',
                    'city' => 'testCity',
                    'zipcode' => '012345'
                ],
                'shippingaddress' => [
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'countryId' => 1,
                    'street' => 'test 123',
                    'city' => 'testCity',
                    'zipcode' => '012345'
                ],
                'additional' => [
                    'user' => [
                        'email' => 'test@email.com'
                    ]
                ]
            ],
            [],
            'testToken',
            null,
            null,
            null,
            null,
            new GooglePayStruct(null, null, null),
            new ApplePayStruct(null, null, null, null, null, null)
        );
    }
}