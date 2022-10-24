<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\CheckoutApi\Request;

use CkoCheckoutPayment\Components\CheckoutApi\Request\PaymentRequestHandlerService;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\PaymentMethods\BancontactPaymentMethod;
use CkoCheckoutPayment\Tests\Mocks\BancontactPaymentRequestServiceMock;
use PHPUnit\Framework\TestCase;

class PaymentRequestHandlerServiceTest extends TestCase
{
    /**
     * @var PaymentRequestHandlerService
     */
    private $paymentRequestHandlerService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentRequestHandlerService = new PaymentRequestHandlerService();
        $this->paymentRequestHandlerService->addPaymentRequestService(new BancontactPaymentRequestServiceMock());
    }

    /**
     * @dataProvider paymentRequestDataProvider
     */
    public function testHandlePaymentRequest(string $paymentMethodName): void
    {
        /** @var PaymentRequestStruct $paymentRequestStruct */
        $paymentRequestStruct = $this->createMock(PaymentRequestStruct::class);
        $paymentRequestStruct->method('getPaymentMethodName')->willReturn($paymentMethodName);

        $request = $this->paymentRequestHandlerService->handlePaymentRequest($paymentRequestStruct);

        static::assertInstanceOf(PaymentResponseStruct::class, $request);
    }

    public function testHandlingPaymentRequestFromInvalidPaymentMethodThrowsException(): void
    {
        static::expectException(\RuntimeException::class);
        static::expectExceptionMessage('Sending payment request for payment method not_supported is not supported.');

        /** @var PaymentRequestStruct $paymentRequestStruct */
        $paymentRequestStruct = $this->createMock(PaymentRequestStruct::class);
        $paymentRequestStruct->method('getPaymentMethodName')->willReturn('not_supported');

        $this->paymentRequestHandlerService->handlePaymentRequest($paymentRequestStruct);
    }

    public function paymentRequestDataProvider(): array
    {
        return [
            [
                'paymentMethodName' => BancontactPaymentMethod::NAME
            ]
        ];
    }
}