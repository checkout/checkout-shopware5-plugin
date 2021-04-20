<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment;

class PaymentDetailsResponseStruct
{
    /**
     * @var string
     */
    private $paymentId;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var \DateTimeImmutable
     */
    private $plannedDebitDate;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $status;

    /**
     * @var null|array
     */
    private $source;

    /**
     * @var bool
     */
    private $isSuccessful;

    public function __construct(
        string $paymentId,
        string $reference,
        \DateTimeImmutable $plannedDebitDate,
        string $currency,
        float $amount,
        string $status,
        ?array $source,
        bool $isSuccessful
    ) {
        $this->paymentId = $paymentId;
        $this->reference = $reference;
        $this->plannedDebitDate = $plannedDebitDate;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->status = $status;
        $this->source = $source;
        $this->isSuccessful = $isSuccessful;
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getPlannedDebitDate(): \DateTimeImmutable
    {
        return $this->plannedDebitDate;
    }

    public function setPlannedDebitDate(\DateTimeImmutable $plannedDebitDate): void
    {
        $this->plannedDebitDate = $plannedDebitDate;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getSource(): ?array
    {
        return $this->source;
    }

    public function setSource(?array $source): void
    {
        $this->source = $source;
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    public function setIsSuccessful(bool $isSuccessful): void
    {
        $this->isSuccessful = $isSuccessful;
    }
}
