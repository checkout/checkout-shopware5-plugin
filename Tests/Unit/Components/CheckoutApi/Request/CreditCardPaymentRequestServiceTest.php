<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\CheckoutApi\Request;

use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Request\CreditCardPaymentRequestService;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\ApplePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\GooglePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\Logger\LoggerService;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Tests\Mocks\Api\CreditCard\CreditCardPaymentMock;
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

class CreditCardPaymentRequestServiceTest extends TestCase
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
     * @var CreditCardPaymentRequestService
     */
    private $creditCardPaymentRequestService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentSessionServiceFactoryMock = new PaymentSessionServiceFactoryMock($this->createMock(Container::class));
        $this->paymentSessionServiceFactoryMock->createPaymentSessionService();

        $this->apiClientServiceMock = new CheckoutApiClientServiceMock(CreditCardPaymentMethod::NAME);
        $this->creditCardPaymentRequestService = new CreditCardPaymentRequestService(
            $this->apiClientServiceMock,
            new ConfigurationServiceMock(),
            new DependencyProviderServiceMock(),
            $this->paymentSessionServiceFactoryMock,
            $this->createMock(LoggerService::class),
            new CountryRepositoryMock($this->createMock(ModelManager::class), new ClassMetadata(Country::class))
        );
    }

    public function testSendPaymentRequestWithToken(): void
    {
        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentResponse = $this->creditCardPaymentRequestService->sendPaymentRequest($paymentRequestStruct);

        static::assertInstanceOf(PaymentResponseStruct::class, $paymentResponse);
        static::assertSame([
            'id' => 'src_abcabc',
            'type' => 'card',
            'billing_address' => [
                'address_line1' => 'test 123',
                'city' => 'testCity',
                'zip' => '012345',
                'country' => 'DE'
            ],
            'expiry_month' => 1,
            'expiry_year' => 2024,
            'scheme' => 'Visa',
            'last4' => '4242',
            'fingerprint' => 'FE7F4E9B5E889A7D76924B0A011B16575C7FD05E4CA0B942C1C9B93766903972',
            'bin' => '424242',
            'card_type' => 'Credit',
            'card_category' => 'Consumer',
            'issuer' => 'JPMORGAN CHASE BANK NA',
            'issuer_country' => 'US',
            'product_id' => 'A',
            'product_type' => 'Visa Traditional',
            'avs_check' => 'S',
            'cvv_check' => 'Y',
            'payouts' => true,
            'fast_funds' => 'd'
        ], $paymentResponse->getSource());
        static::assertSame('EUR', $paymentResponse->getCurrency());
        static::assertSame('pay_abcabc', $paymentResponse->getPaymentId());
        static::assertSame('act_abcabc', $paymentResponse->getActionId());
        static::assertSame(500, $paymentResponse->getAmount());
        static::assertTrue($paymentResponse->getApproved());
        static::assertSame('Authorized', $paymentResponse->getStatus());
        static::assertSame('123456', $paymentResponse->getAuthCode());
        static::assertSame('05', $paymentResponse->getEci());
        static::assertSame('000000000000000', $paymentResponse->getSchemeId());
        static::assertSame('10000', $paymentResponse->getResponseCode());
        static::assertSame('Approved', $paymentResponse->getResponseSummary());
        static::assertSame(['flagged' => false], $paymentResponse->getRisk());
        static::assertSame(['id' => 'cus_abcabc', 'email' => 'test@email.com', 'name' => 'firstname lastname'], $paymentResponse->getCustomer());
        static::assertSame('2021-10-04T14:49:48Z', $paymentResponse->getProcessedOn());
        static::assertSame('testReference', $paymentResponse->getReference());
        static::assertSame(['acquirer_transaction_id' => '1234567890', 'retrieval_reference_number' => '123456789012'], $paymentResponse->getProcessing());
        static::assertSame(201, $paymentResponse->getHttpCode());
    }

    public function testSendPaymentRequestWithSourceId(): void
    {
        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentRequestStruct->setToken(null);
        $paymentRequestStruct->setSourceId('src_abcabc');

        $paymentResponse = $this->creditCardPaymentRequestService->sendPaymentRequest($paymentRequestStruct);

        static::assertInstanceOf(PaymentResponseStruct::class, $paymentResponse);
        static::assertSame([
            'id' => 'src_abcabc',
            'type' => 'card',
            'billing_address' => [
                'address_line1' => 'test 123',
                'city' => 'testCity',
                'zip' => '012345',
                'country' => 'DE'
            ],
            'expiry_month' => 1,
            'expiry_year' => 2024,
            'scheme' => 'Visa',
            'last4' => '4242',
            'fingerprint' => 'FE7F4E9B5E889A7D76924B0A011B16575C7FD05E4CA0B942C1C9B93766903972',
            'bin' => '424242',
            'card_type' => 'Credit',
            'card_category' => 'Consumer',
            'issuer' => 'JPMORGAN CHASE BANK NA',
            'issuer_country' => 'US',
            'product_id' => 'A',
            'product_type' => 'Visa Traditional',
            'avs_check' => 'S',
            'cvv_check' => 'Y',
            'payouts' => true,
            'fast_funds' => 'd'
        ], $paymentResponse->getSource());
        static::assertSame('EUR', $paymentResponse->getCurrency());
        static::assertSame('pay_abcabc', $paymentResponse->getPaymentId());
        static::assertSame('act_abcabc', $paymentResponse->getActionId());
        static::assertSame(500, $paymentResponse->getAmount());
        static::assertTrue($paymentResponse->getApproved());
        static::assertSame('Authorized', $paymentResponse->getStatus());
        static::assertSame('123456', $paymentResponse->getAuthCode());
        static::assertSame('05', $paymentResponse->getEci());
        static::assertSame('000000000000000', $paymentResponse->getSchemeId());
        static::assertSame('10000', $paymentResponse->getResponseCode());
        static::assertSame('Approved', $paymentResponse->getResponseSummary());
        static::assertSame(['flagged' => false], $paymentResponse->getRisk());
        static::assertSame(['id' => 'cus_abcabc', 'email' => 'test@email.com', 'name' => 'firstname lastname'], $paymentResponse->getCustomer());
        static::assertSame('2021-10-04T14:49:48Z', $paymentResponse->getProcessedOn());
        static::assertSame('testReference', $paymentResponse->getReference());
        static::assertSame(['acquirer_transaction_id' => '1234567890', 'retrieval_reference_number' => '123456789012'], $paymentResponse->getProcessing());
        static::assertSame(201, $paymentResponse->getHttpCode());
    }

    public function testSendPaymentRequestThrowsExceptionWhenApiExceptionIsThrown(): void
    {
        static::expectException(CheckoutApiRequestException::class);
        static::expectExceptionMessage('Checkout.com API Request has failed for the reason: '. CreditCardPaymentMock::class);

        $this->apiClientServiceMock->setShouldThrowApiException(true);

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $this->creditCardPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testSendPaymentRequestThrowsExceptionWhenRequiredPaymentDetailsMissing(): void
    {
        static::expectException(RequiredPaymentDetailsMissingException::class);
        static::expectExceptionMessage('Checkout.com request has failed for the reason: required payment details for cko_cc missing.');

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentRequestStruct->setToken(null);
        $this->creditCardPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testIsPaymentSessionValidWithToken(): void
    {
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::TOKEN, 'testToken');
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::SOURCE_ID, '');

        static::assertTrue($this->creditCardPaymentRequestService->isPaymentSessionValid());
    }

    public function testIsPaymentSessionValidWithSourceId(): void
    {
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::TOKEN, '');
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::SOURCE_ID, 'testSourceId');

        static::assertTrue($this->creditCardPaymentRequestService->isPaymentSessionValid());
    }

    public function testIsPaymentSessionValidWithNoTokenAndSourceId(): void
    {
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::TOKEN, '');
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::SOURCE_ID, '');

        static::assertFalse($this->creditCardPaymentRequestService->isPaymentSessionValid());
    }

    private function getTestPaymentRequestStruct(): PaymentRequestStruct
    {
        return new PaymentRequestStruct(
            CreditCardPaymentMethod::NAME,
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