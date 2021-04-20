<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\PaymentStatusMapper;

use CkoCheckoutPayment\Components\CheckoutApi\CheckoutApiPaymentStatus;
use CkoCheckoutPayment\Components\PaymentStatusMapper\PaymentStatusMapperService;
use CkoCheckoutPayment\Components\PaymentStatusMapper\PaymentStatusMapperServiceInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Models\Order\Status as OrderStatus;

class PaymentStatusMapperServiceTest extends TestCase
{
    /**
     * @var PaymentStatusMapperServiceInterface
     */
    private $paymentStatusMapperService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentStatusMapperService = new PaymentStatusMapperService();
    }

    /**
     * @dataProvider statusDataProvider
     */
    public function testMapStatus(string $apiStatus, int $expectedStatus)
    {
        static::assertSame($expectedStatus, $this->paymentStatusMapperService->mapStatus($apiStatus));
    }

    /**
     * @dataProvider invalidStatusDataProvider
     */
    public function testMappingUnknownStatusReturnsReviewNecessaryPaymentStatus(string $apiStatus, int $expectedStatus)
    {
        static::assertSame($expectedStatus, $this->paymentStatusMapperService->mapStatus($apiStatus));
    }

    public function statusDataProvider()
    {
        return [
            [CheckoutApiPaymentStatus::API_PAYMENT_PENDING, OrderStatus::PAYMENT_STATE_OPEN],
            [CheckoutApiPaymentStatus::API_PAYMENT_APPROVED, OrderStatus::PAYMENT_STATE_THE_CREDIT_HAS_BEEN_ACCEPTED],
            [CheckoutApiPaymentStatus::API_PAYMENT_DECLINED, OrderStatus::PAYMENT_STATE_NO_CREDIT_APPROVED],
            [CheckoutApiPaymentStatus::API_PAYMENT_CAPTURED, OrderStatus::PAYMENT_STATE_COMPLETELY_PAID],
            [CheckoutApiPaymentStatus::API_PAYMENT_CAPTURED_PARTIALLY, OrderStatus::PAYMENT_STATE_PARTIALLY_PAID],
            [CheckoutApiPaymentStatus::API_PAYMENT_REFUNDED, OrderStatus::PAYMENT_STATE_RE_CREDITING],
            [CheckoutApiPaymentStatus::API_PAYMENT_REFUNDED_PARTIALLY, OrderStatus::PAYMENT_STATE_RE_CREDITING],
            [CheckoutApiPaymentStatus::API_PAYMENT_VOID, OrderStatus::PAYMENT_STATE_THE_PROCESS_HAS_BEEN_CANCELLED],
            [CheckoutApiPaymentStatus::API_PAYMENT_EXPIRED, OrderStatus::PAYMENT_STATE_DELAYED],
            [CheckoutApiPaymentStatus::API_PAYMENT_CAPTURE_PENDING, OrderStatus::PAYMENT_STATE_OPEN],
        ];
    }

    public function invalidStatusDataProvider()
    {
        return [
            ['test', OrderStatus::PAYMENT_STATE_REVIEW_NECESSARY],
        ];
    }
}
