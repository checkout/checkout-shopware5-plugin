<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment;

class PaymentActionStruct
{
    public const TYPE_CAPTURE = 'capture';
    public const TYPE_VOID = 'void';
    public const TYPE_REFUND = 'refund';

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var \DateTimeImmutable
     */
    private $date;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var bool
     */
    private $isApproved;

    /**
     * @var string
     */
    private $reference;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function isApproved(): bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): void
    {
        $this->isApproved = $isApproved;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => mb_strtolower($this->type),
            'date' => $this->date->format(DATE_RFC822),
            'amount' => $this->amount,
            'isApproved' => $this->isApproved,
            'reference' => $this->reference,
        ];
    }
}
