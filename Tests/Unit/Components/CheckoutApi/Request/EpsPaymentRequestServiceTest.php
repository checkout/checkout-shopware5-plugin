<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\CheckoutApi\Request;

use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Request\EpsPaymentRequestService;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\ApplePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\GooglePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\Logger\LoggerService;
use CkoCheckoutPayment\Components\PaymentMethods\EpsPaymentMethod;
use CkoCheckoutPayment\Tests\Mocks\Api\Eps\EpsPaymentMock;
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

class EpsPaymentRequestServiceTest extends TestCase
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
     * @var EpsPaymentRequestService
     */
    private $epsPaymentRequestService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentSessionServiceFactoryMock = new PaymentSessionServiceFactoryMock($this->createMock(Container::class));
        $this->paymentSessionServiceFactoryMock->createPaymentSessionService();

        $this->apiClientServiceMock = new CheckoutApiClientServiceMock(EpsPaymentMethod::NAME);
        $this->epsPaymentRequestService = new EpsPaymentRequestService(
            $this->apiClientServiceMock,
            new ConfigurationServiceMock(),
            new DependencyProviderServiceMock(),
            $this->paymentSessionServiceFactoryMock,
            $this->createMock(LoggerService::class),
            new CountryRepositoryMock($this->createMock(ModelManager::class), new ClassMetadata(Country::class))
        );
    }

    public function testSendPaymentRequest(): void
    {
        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentResponse = $this->epsPaymentRequestService->sendPaymentRequest($paymentRequestStruct);

        static::assertInstanceOf(PaymentResponseStruct::class, $paymentResponse);
        static::assertSame('pay_abcabc', $paymentResponse->getPaymentId());
        static::assertSame('Pending', $paymentResponse->getStatus());
        static::assertSame('testReference', $paymentResponse->getReference());
        static::assertSame(['id' => 'cus_abcabc', 'email' => 'test@email.com', 'name' => 'firstname lastname'], $paymentResponse->getCustomer());
        static::assertSame('https://api.sandbox.checkout.com/payments/pay_abcabc', $paymentResponse->getRedirectionUrl());
        static::assertSame(202, $paymentResponse->getHttpCode());
    }

    public function testSendPaymentRequestThrowsExceptionWhenApiExceptionIsThrown(): void
    {
        static::expectException(CheckoutApiRequestException::class);
        static::expectExceptionMessage('Checkout.com API Request has failed for the reason: ' . EpsPaymentMock::class);

        $this->apiClientServiceMock->setShouldThrowApiException(true);

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $this->epsPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testSendPaymentRequestThrowsExceptionWhenRequiredPaymentDetailsPurposeMissing(): void
    {
        static::expectException(RequiredPaymentDetailsMissingException::class);
        static::expectExceptionMessage('Checkout.com request has failed for the reason: required payment details for ' . EpsPaymentMethod::NAME . ' missing.');

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentRequestStruct->setPurpose('');
        $this->epsPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testIsPaymentSessionValid(): void
    {
        static::assertTrue($this->epsPaymentRequestService->isPaymentSessionValid());
    }

    private function getTestPaymentRequestStruct(): PaymentRequestStruct
    {
        return new PaymentRequestStruct(
            EpsPaymentMethod::NAME,
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
            null,
            null,
            null,
            null,
            null,
            new GooglePayStruct(null, null, null),
            new ApplePayStruct(null, null, null, null, null, null)
        );
    }
}