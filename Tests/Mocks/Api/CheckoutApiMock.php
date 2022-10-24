<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks\Api;

use CkoCheckoutPayment\Components\PaymentMethods\ApplePayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\BancontactPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\CreditCardPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\EpsPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\GiropayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\GooglePayPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\IdealPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\KlarnaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\PayPalPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\Przelewy24PaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\SepaPaymentMethod;
use CkoCheckoutPayment\Components\PaymentMethods\SofortPaymentMethod;
use CkoCheckoutPayment\Tests\Mocks\Api\ApplePay\ApplePayPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\ApplePay\ApplePayTokenMock;
use CkoCheckoutPayment\Tests\Mocks\Api\Bancontact\BancontactPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\CreditCard\CreditCardPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\Eps\EpsPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\Giropay\GiropayPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\GooglePay\GooglePayPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\GooglePay\GooglePayTokenMock;
use CkoCheckoutPayment\Tests\Mocks\Api\Ideal\IdealPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\Klarna\KlarnaPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\Klarna\KlarnaSourceMock;
use CkoCheckoutPayment\Tests\Mocks\Api\PayPal\PayPalPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\Przelewy24\Przelewy24PaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\Sepa\SepaPaymentMock;
use CkoCheckoutPayment\Tests\Mocks\Api\Sepa\SepaSourceMock;
use CkoCheckoutPayment\Tests\Mocks\Api\Sofort\SofortPaymentMock;

class CheckoutApiMock
{
    /**
     * @var string
     */
    private $paymentMethodName;

    /**
     * @var bool
     */
    private $shouldThrowApiException;

    /**
     * @var bool
     */
    private $shouldThrowTokenException;

    /**
     * @var bool
     */
    private $shouldThrowSourceException;

    public function __construct(string $paymentMethodName, bool $shouldThrowApiException, bool $shouldThrowTokenException, bool $shouldThrowSourceException)
    {
        $this->paymentMethodName = $paymentMethodName;
        $this->shouldThrowApiException = $shouldThrowApiException;
        $this->shouldThrowTokenException = $shouldThrowTokenException;
        $this->shouldThrowSourceException = $shouldThrowSourceException;
    }

    public function sources()
    {
        if ($this->paymentMethodName === SepaPaymentMethod::NAME) {
            return new SepaSourceMock();
        }

        if ($this->paymentMethodName === KlarnaPaymentMethod::NAME) {
            return new KlarnaSourceMock($this->shouldThrowSourceException);
        }
    }

    public function tokens()
    {
        if ($this->paymentMethodName === ApplePayPaymentMethod::NAME) {
            return new ApplePayTokenMock($this->shouldThrowTokenException);
        }

        if ($this->paymentMethodName === GooglePayPaymentMethod::NAME) {
            return new GooglePayTokenMock($this->shouldThrowTokenException);
        }

        throw new \InvalidArgumentException(
            sprintf(
                'mock tokens requests are only supported by: %s, %s',
                ApplePayPaymentMethod::NAME,
                GooglePayPaymentMethod::NAME
            )
        );
    }

    public function payments()
    {
        if ($this->paymentMethodName === CreditCardPaymentMethod::NAME) {
            return new CreditCardPaymentMock($this->shouldThrowApiException);
        }

        if ($this->paymentMethodName === PayPalPaymentMethod::NAME) {
            return new PayPalPaymentMock($this->shouldThrowApiException);
        }

        if ($this->paymentMethodName === KlarnaPaymentMethod::NAME) {
            return new KlarnaPaymentMock($this->shouldThrowApiException);
        }

        if ($this->paymentMethodName === GiropayPaymentMethod::NAME) {
            return new GiropayPaymentMock($this->shouldThrowApiException);
        }

        if ($this->paymentMethodName === SofortPaymentMethod::NAME) {
            return new SofortPaymentMock($this->shouldThrowApiException);
        }

        if ($this->paymentMethodName === BancontactPaymentMethod::NAME) {
            return new BancontactPaymentMock($this->shouldThrowApiException);
        }

        if ($this->paymentMethodName === EpsPaymentMethod::NAME) {
            return new EpsPaymentMock($this->shouldThrowApiException);
        }

        if ($this->paymentMethodName === IdealPaymentMethod::NAME) {
            return new IdealPaymentMock($this->shouldThrowApiException);
        }

        if ($this->paymentMethodName === Przelewy24PaymentMethod::NAME) {
            return new Przelewy24PaymentMock($this->shouldThrowApiException);
        }

        if ($this->paymentMethodName === ApplePayPaymentMethod::NAME) {
            return new ApplePayPaymentMock($this->shouldThrowApiException);
        }

        if ($this->paymentMethodName === GooglePayPaymentMethod::NAME) {
            return new GooglePayPaymentMock($this->shouldThrowApiException);
        }

        return new SepaPaymentMock($this->shouldThrowApiException);
    }
}