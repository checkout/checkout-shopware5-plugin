<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\PaymentMethodValidator;

use CkoCheckoutPayment\Components\PaymentMethods\BancontactPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethodValidator\PaymentMethodValidatorService;
use CkoCheckoutPayment\Components\PaymentMethodValidator\PaymentMethodValidatorServiceInterface;
use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionService;
use CkoCheckoutPayment\Tests\Mocks\BancontactPaymentRequestServiceMock;
use PHPUnit\Framework\TestCase;

class PaymentMethodValidatorServiceTest extends TestCase
{
    /**
     * @var PaymentMethodValidatorServiceInterface
     */
    private $paymentMethodValidatorService;

    public function setUp(): void
    {
        parent::setUp();

        $paymentSessionServiceMock = $this->createMock(PaymentSessionService::class);
        $this->paymentMethodValidatorService = new PaymentMethodValidatorService($paymentSessionServiceMock);
        $this->paymentMethodValidatorService->addPaymentRequestService(
            new BancontactPaymentRequestServiceMock()
        );
    }

    public function testIsCheckoutPaymentMethod(): void
    {
        static::assertTrue($this->paymentMethodValidatorService->isCheckoutPaymentMethod(BancontactPaymentMethod::NAME));
    }

    public function testIsNotCheckoutPaymentMethod(): void
    {
        static::assertFalse($this->paymentMethodValidatorService->isCheckoutPaymentMethod('test'));
    }

    public function testIsPaymentMethodValid(): void
    {
        static::assertTrue($this->paymentMethodValidatorService->isPaymentMethodValid(BancontactPaymentMethod::NAME));
    }

    public function testPaymentMethodIsNotValidUnknownPaymentMethodName(): void
    {
        static::assertFalse($this->paymentMethodValidatorService->isPaymentMethodValid('unknown'));
    }
}