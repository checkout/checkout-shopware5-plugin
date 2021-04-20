<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks;

use Checkout\Models\Payments\Payment;
use Checkout\Models\Payments\SofortSource;
use CkoCheckoutPayment\Components\CheckoutApi\Request\PaymentRequestServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentRequestStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentResponseStruct;
use CkoCheckoutPayment\Components\PaymentMethods\BancontactPaymentMethod;

class BancontactPaymentRequestServiceMock implements PaymentRequestServiceInterface
{
    public function supportsPaymentRequest(string $paymentMethodName): bool
    {
        return $paymentMethodName === BancontactPaymentMethod::NAME;
    }

    public function sendPaymentRequest(PaymentRequestStruct $paymentRequestStruct): PaymentResponseStruct
    {
        $payment = new Payment(new SofortSource(), $paymentRequestStruct->getCurrency());

        return new PaymentResponseStruct($payment);
    }

    public function isPaymentSessionValid(): bool
    {
        return true;
    }
}