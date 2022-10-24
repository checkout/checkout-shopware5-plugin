<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\CheckoutApi\Request;

use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Request\GooglePayPaymentRequestService;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\ApplePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\GooglePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\Logger\LoggerService;
use CkoCheckoutPayment\Components\PaymentMethods\GooglePayPaymentMethod;
use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Tests\Mocks\Api\GooglePay\GooglePayPaymentMock;
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

class GooglePayPaymentRequestServiceTest extends TestCase
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
     * @var GooglePayPaymentRequestService
     */
    private $googlePayPaymentRequestService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentSessionServiceFactoryMock = new PaymentSessionServiceFactoryMock($this->createMock(Container::class));
        $this->paymentSessionServiceFactoryMock->createPaymentSessionService();

        $this->apiClientServiceMock = new CheckoutApiClientServiceMock(GooglePayPaymentMethod::NAME);
        $this->googlePayPaymentRequestService = new GooglePayPaymentRequestService(
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
        $paymentResponse = $this->googlePayPaymentRequestService->sendPaymentRequest($paymentRequestStruct);

        static::assertInstanceOf(PaymentResponseStruct::class, $paymentResponse);

        static::assertSame('EUR', $paymentResponse->getCurrency());
        static::assertSame('pay_abcabc', $paymentResponse->getPaymentId());
        static::assertSame('act_abcabc', $paymentResponse->getActionId());
        static::assertSame(500, $paymentResponse->getAmount());
        static::assertTrue($paymentResponse->getApproved());
        static::assertSame('Authorized', $paymentResponse->getStatus());
        static::assertSame('10000', $paymentResponse->getResponseCode());
        static::assertSame('Approved', $paymentResponse->getResponseSummary());
        static::assertSame(['id' => 'cus_abcabc', 'email' => 'test@email.com', 'name' => 'firstname lastname'], $paymentResponse->getCustomer());
        static::assertSame('2021-10-05T12:13:01Z', $paymentResponse->getProcessedOn());
        static::assertSame('testReference', $paymentResponse->getReference());
        static::assertSame(201, $paymentResponse->getHttpCode());
    }

    public function testSendPaymentRequestThrowsExceptionWhenApiExceptionIsThrown(): void
    {
        static::expectException(CheckoutApiRequestException::class);
        static::expectExceptionMessage('Checkout.com API Request has failed for the reason: ' . GooglePayPaymentMock::class);

        $this->apiClientServiceMock->setShouldThrowApiException(true);

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $this->googlePayPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testSendPaymentRequestThrowsExceptionWhenRequiredPaymentDetailsTokenMissing(): void
    {
        static::expectException(RequiredPaymentDetailsMissingException::class);
        static::expectExceptionMessage('Checkout.com request has failed for the reason: required payment details for ' . GooglePayPaymentMethod::NAME . ' missing.');

        $this->apiClientServiceMock->setShouldThrowTokenException(true);

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $this->googlePayPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testSendPaymentRequestThrowsExceptionWhenRequiredPaymentDetailsProcotolVersionMissing(): void
    {
        static::expectException(RequiredPaymentDetailsMissingException::class);
        static::expectExceptionMessage('Checkout.com request has failed for the reason: required payment details for ' . GooglePayPaymentMethod::NAME . ' missing.');

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentRequestStruct->getGooglePayStruct()->setProtocolVersion('');
        $this->googlePayPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testSendPaymentRequestThrowsExceptionWhenRequiredPaymentDetailsSignatureMissing(): void
    {
        static::expectException(RequiredPaymentDetailsMissingException::class);
        static::expectExceptionMessage('Checkout.com request has failed for the reason: required payment details for ' . GooglePayPaymentMethod::NAME . ' missing.');

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentRequestStruct->getGooglePayStruct()->setSignature('');
        $this->googlePayPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testSendPaymentRequestThrowsExceptionWhenRequiredPaymentDetailsSignedMessageMissing(): void
    {
        static::expectException(RequiredPaymentDetailsMissingException::class);
        static::expectExceptionMessage('Checkout.com request has failed for the reason: required payment details for ' . GooglePayPaymentMethod::NAME . ' missing.');

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentRequestStruct->getGooglePayStruct()->setSignedMessage('');
        $this->googlePayPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testIsPaymentSessionValidWithValidValues(): void
    {
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::GOOGLE_PAY_SIGNATURE, 'testSignature');
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::GOOGLE_PAY_PROTOCOL_VERSION, 'testProtocolVersion');
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::GOOGLE_PAY_SIGNED_MESSAGE, 'testSignedMessage');
        static::assertTrue($this->googlePayPaymentRequestService->isPaymentSessionValid());
    }

    public function testIsPaymentSessionValidWithValidInvalidValues(): void
    {
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::GOOGLE_PAY_SIGNATURE, '');
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::GOOGLE_PAY_PROTOCOL_VERSION, '');
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::GOOGLE_PAY_SIGNED_MESSAGE, '');
        static::assertFalse($this->googlePayPaymentRequestService->isPaymentSessionValid());
    }

    private function getTestPaymentRequestStruct(): PaymentRequestStruct
    {
        return new PaymentRequestStruct(
            GooglePayPaymentMethod::NAME,
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
            new GooglePayStruct('testSignature', 'testProcotolVersion', 'testSignedMessage'),
            new ApplePayStruct(null, null, null, null, null, null)
        );
    }
}