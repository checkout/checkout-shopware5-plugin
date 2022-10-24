<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks;

use CkoCheckoutPayment\Components\CheckoutApi\ApiClient\CheckoutApiClientServiceInterface;
use CkoCheckoutPayment\Tests\Mocks\Api\CheckoutApiMock;

class CheckoutApiClientServiceMock implements CheckoutApiClientServiceInterface
{
    /**
     * @var string
     */
    private $paymentMethodName;

    /**
     * @var CheckoutApiMock
     */
    private $checkoutApiMock;

    /**
     * @var bool
     */
    private $shouldThrowApiException = false;

    /**
     * @var bool
     */
    private $shouldThrowTokenException = false;

    /**
     * @var bool
     */
    private $shouldThrowSourceException = false;

    public function __construct(string $paymentMethodName)
    {
        $this->paymentMethodName = $paymentMethodName;
    }

    public function createClient(?int $shopId)
    {
        if (!$this->checkoutApiMock instanceof CheckoutApiMock) {
            $this->checkoutApiMock = new CheckoutApiMock($this->paymentMethodName, $this->shouldThrowApiException, $this->shouldThrowTokenException, $this->shouldThrowSourceException);
        }

        return $this->checkoutApiMock;
    }

    public function getPublicKey(?int $shopId): ?string
    {
        return 'testPublicKey';
    }

    public function isSandboxMode(?int $shopId): bool
    {
        return true;
    }

    public function isShouldThrowApiException(): bool
    {
        return $this->shouldThrowApiException;
    }

    public function setShouldThrowApiException(bool $shouldThrowApiException): void
    {
        $this->shouldThrowApiException = $shouldThrowApiException;
    }

    public function isShouldThrowTokenException(): bool
    {
        return $this->shouldThrowTokenException;
    }

    public function setShouldThrowTokenException(bool $shouldThrowTokenException): void
    {
        $this->shouldThrowTokenException = $shouldThrowTokenException;
    }

    /**
     * @return bool
     */
    public function isShouldThrowSourceException(): bool
    {
        return $this->shouldThrowSourceException;
    }

    /**
     * @param bool $shouldThrowSourceException
     */
    public function setShouldThrowSourceException(bool $shouldThrowSourceException): void
    {
        $this->shouldThrowSourceException = $shouldThrowSourceException;
    }
}