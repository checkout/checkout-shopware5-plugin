<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\Structs;

class CardStruct
{
    /**
     * @var int
     */
    private $customerId;

    /**
     * @var string
     */
    private $sourceId;

    /**
     * @var string
     */
    private $lastFour;

    /**
     * @var string
     */
    private $expiryMonth;

    /**
     * @var string
     */
    private $expiryYear;

    /**
     * @var string
     */
    private $scheme;

    public function __construct(
        int $customerId,
        string $sourceId,
        string $lastFour,
        string $expiryMonth,
        string $expiryYear,
        string $scheme
    ) {
        $this->customerId = $customerId;
        $this->sourceId = $sourceId;
        $this->lastFour = $lastFour;
        $this->expiryMonth = $expiryMonth;
        $this->expiryYear = $expiryYear;
        $this->scheme = $scheme;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     */
    public function setCustomerId(int $customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * @return string
     */
    public function getSourceId(): string
    {
        return $this->sourceId;
    }

    /**
     * @param string $sourceId
     */
    public function setSourceId(string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    /**
     * @return string
     */
    public function getLastFour(): string
    {
        return $this->lastFour;
    }

    /**
     * @param string $lastFour
     */
    public function setLastFour(string $lastFour): void
    {
        $this->lastFour = $lastFour;
    }

    /**
     * @return string
     */
    public function getExpiryMonth(): string
    {
        return $this->expiryMonth;
    }

    /**
     * @param string $expiryMonth
     */
    public function setExpiryMonth(string $expiryMonth): void
    {
        $this->expiryMonth = $expiryMonth;
    }

    /**
     * @return string
     */
    public function getExpiryYear(): string
    {
        return $this->expiryYear;
    }

    /**
     * @param string $expiryYear
     */
    public function setExpiryYear(string $expiryYear): void
    {
        $this->expiryYear = $expiryYear;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @param string $scheme
     */
    public function setScheme(string $scheme): void
    {
        $this->scheme = $scheme;
    }
}