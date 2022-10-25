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

    public function getCurrency(): ?string
    {
        return $this->paymentResponse->getValue('currency');
    }

    public function getCustomer(): array
    {
        return $this->paymentResponse->getValue('customer') ?? [];
    }

    public function getSource(): array
    {
        return $this->paymentResponse->getValue('source') ?? [];
    }

    public function getActionId(): ?string
    {
        return $this->paymentResponse->getValue('action_id');
    }

    public function getAmount(): ?int
    {
        return $this->paymentResponse->getValue('amount');
    }

    public function getApproved(): bool
    {
        return $this->paymentResponse->getValue('approved');
    }

    public function getAuthCode(): ?string
    {
        return $this->paymentResponse->getValue('auth_code');
    }

    public function getEci(): ?string
    {
        return $this->paymentResponse->getValue('eci');
    }

    public function getSchemeId(): ?string
    {
        return $this->paymentResponse->getValue('scheme_id');
    }

    public function getResponseSummary(): ?string
    {
        return $this->paymentResponse->getValue('response_summary');
    }

    public function getProcessedOn(): ?string
    {
        return $this->paymentResponse->getValue('processed_on');
    }

    public function getRisk(): array
    {
        return $this->paymentResponse->getValue('risk') ?? [];
    }

    public function getProcessing(): array
    {
        return $this->paymentResponse->getValue('processing') ?? [];
    }

    public function getHttpCode(): ?int
    {
        return $this->paymentResponse->getValue('http_code') ?? null;
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
