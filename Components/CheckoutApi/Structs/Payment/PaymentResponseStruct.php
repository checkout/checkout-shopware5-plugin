<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment;

use Checkout\Models\Payments\Payment;
use Checkout\Models\Sources\Source;

class PaymentResponseStruct
{
    /**
     * @var Payment
     */
    private $paymentResponse;

    /**
     * @var null|Source
     */
    private $paymentSource;

    public function __construct(
        Payment $paymentResponse,
        ?Source $paymentSource = null
    ) {
        $this->paymentResponse = $paymentResponse;
        $this->paymentSource = $paymentSource;
    }

    /**
     * @return Source|null
     */
    public function getPaymentSource(): ?Source
    {
        return $this->paymentSource;
    }

    public function getPaymentId(): string
    {
        return $this->paymentResponse->getId();
    }

    public function getSignature(): ?string
    {
        return $this->paymentResponse->getValue('signature');
    }

    public function getReference(): ?string
    {
        return $this->paymentResponse->getValue('reference');
    }

    public function getMandateReference(): ?string
    {
        return $this->paymentSource->getValue('response_data')['mandate_reference'] ?? null;
    }

    public function getThreeDsSessionId(): ?string
    {
        return ""; //TODO: replace with proper value
    }

    public function getResponseCode(): ?string
    {
        return $this->paymentResponse->getValue('response_code');
    }

    public function getStatus(): string
    {
        //TODO map with enums
        return $this->paymentResponse->getValue('status');
    }

    public function isSuccessful(): bool
    {
        return $this->paymentResponse->isSuccessful();
    }

    public function getRedirectionUrl(): ?string
    {
        return $this->paymentResponse->getRedirection();
    }
}
