<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\CheckoutApi\Request;

use Checkout\Models\Sources\SepaAddress;
use Checkout\Models\Sources\SepaData;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\RequiredPaymentDetailsMissingException;
use CkoCheckoutPayment\Components\CheckoutApi\Request\SepaPaymentRequestService;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\ApplePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\GooglePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\Logger\LoggerService;
use CkoCheckoutPayment\Components\PaymentMethods\SepaPaymentMethod;
use CkoCheckoutPayment\Components\RequestConstants;
use CkoCheckoutPayment\Tests\Mocks\Api\Sepa\SepaPaymentMock;
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

class SepaPaymentRequestServiceTest extends TestCase
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
     * @var SepaPaymentRequestService
     */
    private $sepaPaymentRequestService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentSessionServiceFactoryMock = new PaymentSessionServiceFactoryMock($this->createMock(Container::class));
        $this->paymentSessionServiceFactoryMock->createPaymentSessionService();

        $this->apiClientServiceMock = new CheckoutApiClientServiceMock(SepaPaymentMethod::NAME);
        $this->sepaPaymentRequestService = new SepaPaymentRequestService(
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
        $paymentResponse = $this->sepaPaymentRequestService->sendPaymentRequest($paymentRequestStruct);

        static::assertInstanceOf(PaymentResponseStruct::class, $paymentResponse);

        static::assertSame('EUR', $paymentResponse->getCurrency());
        static::assertSame('pay_abcabc', $paymentResponse->getPaymentId());
        static::assertSame('Pending', $paymentResponse->getStatus());
        static::assertSame('testReference', $paymentResponse->getReference());
        static::assertSame(202, $paymentResponse->getHttpCode());
        static::assertSame(['id' => 'cus_abcabc', 'email' => 'test@email.com', 'name' => 'test test'], $paymentResponse->getCustomer());

        static::assertSame('sepa', $paymentResponse->getPaymentSource()->getValue('type'));

        $billingAddress = $paymentResponse->getPaymentSource()->getValue('billing_address');
        static::assertInstanceOf(SepaAddress::class, $billingAddress);
        static::assertSame('test', $billingAddress->getValue('address_line1'));
        static::assertSame('test', $billingAddress->getValue('city'));
        static::assertSame('012345', $billingAddress->getValue('zip'));
        static::assertSame('DE', $billingAddress->getValue('country'));
        static::assertSame('testMandateReference', $paymentResponse->getMandateReference());

        $sourceData = $paymentResponse->getPaymentSource()->getValue('source_data');
        static::assertInstanceOf(SepaData::class, $sourceData);
        static::assertSame('test', $sourceData->getValue('first_name'));
        static::assertSame('test', $sourceData->getValue('last_name'));
        static::assertSame('testIban', $sourceData->getValue('account_iban'));
        static::assertSame('testBic', $sourceData->getValue('bic'));
        static::assertSame('testPurpose', $sourceData->getValue('billing_descriptor'));
        static::assertSame(SepaPaymentRequestService::MANDATE_TYPE_SINGLE, $sourceData->getValue('mandate_type'));

        $customer = $paymentResponse->getPaymentSource()->getValue('customer');
        static::assertSame(['id' => 'testId', 'email' => 'test@email.com'], $customer);

        static::assertSame('testSourceId', $paymentResponse->getPaymentSource()->getValue('id'));
        static::assertSame('10000', $paymentResponse->getPaymentSource()->getValue('response_code'));
        static::assertSame('201', $paymentResponse->getPaymentSource()->getValue('http_code'));
        static::assertSame(['mandate_reference' => 'testMandateReference'], $paymentResponse->getPaymentSource()->getValue('response_data'));
    }

    public function testSendPaymentRequestThrowsExceptionWhenApiExceptionIsThrown(): void
    {
        static::expectException(CheckoutApiRequestException::class);
        static::expectExceptionMessage('Checkout.com API Request has failed for the reason: '. SepaPaymentMock::class);

        $this->apiClientServiceMock->setShouldThrowApiException(true);

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $this->sepaPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testSendPaymentRequestThrowsExceptionWhenRequiredPaymentDetailsMissing(): void
    {
        static::expectException(RequiredPaymentDetailsMissingException::class);
        static::expectExceptionMessage('Checkout.com request has failed for the reason: required payment details for cko_sepa missing.');

        $paymentRequestStruct = $this->getTestPaymentRequestStruct();
        $paymentRequestStruct->setIban(null);
        $this->sepaPaymentRequestService->sendPaymentRequest($paymentRequestStruct);
    }

    public function testIsPaymentSessionValidWithIban(): void
    {
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::IBAN, 'testIban');
        static::assertTrue($this->sepaPaymentRequestService->isPaymentSessionValid());
    }

    public function testIsPaymentSessionValidWithInvalidIban(): void
    {
        $this->paymentSessionServiceFactoryMock->getPaymentSessionServiceMock()->set(RequestConstants::IBAN, '');
        static::assertFalse($this->sepaPaymentRequestService->isPaymentSessionValid());
    }

    private function getTestPaymentRequestStruct(): PaymentRequestStruct
    {
        return new PaymentRequestStruct(
            SepaPaymentMethod::NAME,
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
                    'firstname' => 'test',
                    'lastname' => 'test',
                    'countryId' => 1,
                    'street' => 'test',
                    'city' => 'test',
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
            'testBic',
            'testIban',
            SepaPaymentRequestService::MANDATE_TYPE_SINGLE,
            new GooglePayStruct(null, null, null),
            new ApplePayStruct(null, null, null, null, null, null)
        );
    }
}