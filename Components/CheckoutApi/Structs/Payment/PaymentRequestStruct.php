<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment;

use CkoCheckoutPayment\Components\CheckoutApi\Structs\ApplePayStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\GooglePayStruct;

class PaymentRequestStruct
{
    /**
     * @var string
     */
    private $paymentMethodName;

    /**
     * @var bool
     */
    private $isAutoCaptureEnabled;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $purpose;

    /**
     * @var string
     */
    private $successUrl;

    /**
     * @var string
     */
    private $failureUrl;

    /**
     * @var string
     */
    private $basketSignature;

    /**
     * @var array
     */
    private $user = [];

    /**
     * @var array
     */
    private $basket = [];

    /**
     * @var string|null
     */
    private $token;

    /**
     * @var string|null
     */
    private $sourceId;

    /**
     * @var string|null
     */
    private $bic;

	/**
	 * @var string|null
	 */
	private $iban;

	/**
	 * @var string|null
	 */
	private $mandate;

    /**
     * @var GooglePayStruct
     */
	private $googlePayStruct;

    /**
     * @var ApplePayStruct
     */
	private $applePayStruct;

    public function __construct(
        string $paymentMethodName,
        bool $isAutoCaptureEnabled,
        float $amount,
        string $currency,
        string $reference,
        string $purpose,
        string $successUrl,
        string $failureUrl,
        string $basketSignature,
        array $user,
        array $basket,
        ?string $token,
        ?string $sourceId,
        ?string $bic,
        ?string $iban,
        ?string $mandate,
        GooglePayStruct $googlePayStruct,
        ApplePayStruct $applePayStruct
    ) {
        $this->paymentMethodName = $paymentMethodName;
        $this->isAutoCaptureEnabled = $isAutoCaptureEnabled;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->reference = $reference;
        $this->purpose = $purpose;
        $this->successUrl = $successUrl;
        $this->failureUrl = $failureUrl;
        $this->basketSignature = $basketSignature;
        $this->user = $user;
        $this->basket = $basket;
        $this->token = $token;
        $this->sourceId = $sourceId;
        $this->bic = $bic;
        $this->iban = $iban;
		$this->mandate = $mandate;
		$this->googlePayStruct = $googlePayStruct;
        $this->applePayStruct = $applePayStruct;
    }

    public function getPaymentMethodName(): string
    {
        return $this->paymentMethodName;
    }

    public function setPaymentMethodName(string $paymentMethodName): void
    {
        $this->paymentMethodName = $paymentMethodName;
    }

    public function isAutoCaptureEnabled(): bool
    {
        return $this->isAutoCaptureEnabled;
    }

    public function setIsAutoCaptureEnabled(bool $isAutoCaptureEnabled): void
    {
        $this->isAutoCaptureEnabled = $isAutoCaptureEnabled;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function setPurpose(string $purpose): void
    {
        $this->purpose = $purpose;
    }

    public function getSuccessUrl(): string
    {
        return $this->successUrl;
    }

    public function setSuccessUrl(string $successUrl): void
    {
        $this->successUrl = $successUrl;
    }

    public function getFailureUrl(): string
    {
        return $this->failureUrl;
    }

    public function setFailureUrl(string $failureUrl): void
    {
        $this->failureUrl = $failureUrl;
    }

    public function getBasketSignature(): string
    {
        return $this->basketSignature;
    }

    public function setBasketSignature(string $basketSignature): void
    {
        $this->basketSignature = $basketSignature;
    }

    public function getUser(): array
    {
        return $this->user;
    }

    public function setUser(array $user): void
    {
        $this->user = $user;
    }

    public function getBasket(): array
    {
        return $this->basket;
    }

    public function setBasket(array $basket): void
    {
        $this->basket = $basket;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function setSourceId(?string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function getBic(): ?string
    {
        return $this->bic;
    }

    public function setBic(?string $bic): void
    {
        $this->bic = $bic;
    }

    public function getIban(): ?string
    {
        return $this->iban;
    }

    public function setIban(?string $iban): void
    {
        $this->iban = $iban;
    }

    public function getMandate(): ?string
    {
        return $this->mandate;
    }

    public function setMandate(?string $mandate): void
    {
        $this->mandate = $mandate;
    }

    public function getGooglePayStruct(): GooglePayStruct
    {
        return $this->googlePayStruct;
    }

    public function setGooglePayStruct(GooglePayStruct $googlePayStruct): void
    {
        $this->googlePayStruct = $googlePayStruct;
    }

    public function getApplePayStruct(): ApplePayStruct
    {
        return $this->applePayStruct;
    }

    public function setApplePayStruct(ApplePayStruct $applePayStruct): void
    {
        $this->applePayStruct = $applePayStruct;
    }
}
