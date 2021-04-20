<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Unit\Components\CheckoutApi\ResponseBuilder;

use CkoCheckoutPayment\Components\CheckoutApi\Builder\ResponseBuilder\PaymentDetailsResponseBuilderService;
use CkoCheckoutPayment\Components\CheckoutApi\Builder\ResponseBuilder\PaymentDetailsResponseBuilderServiceInterface;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentActionsResponseStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentActionStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentDetailsResponseStruct;
use CkoCheckoutPayment\Components\OrderProvider\OrderProviderService;
use CkoCheckoutPayment\Components\PaymentStatusMapper\PaymentStatusMapperService;
use PHPUnit\Framework\TestCase;
use Shopware\Components\StateTranslatorService;
use Shopware\Models\Order\Status as OrderStatus;

class PaymentDetailsResponseBuilderServiceTest extends TestCase
{
    /**
     * @var PaymentDetailsResponseBuilderServiceInterface
     */
    private $paymentDetailsResponseBuilderService;

    protected function setUp(): void
    {
        parent::setUp();

        $orderProviderServiceMock = $this->createMock(OrderProviderService::class);
        $orderProviderServiceMock->method('getOrderStatusById')->willReturn((string)OrderStatus::PAYMENT_STATE_OPEN);

        $stateTranslatorService = $this->createMock(StateTranslatorService::class);
        $stateTranslatorService->method('translateState')->willReturn(['description' => 'The credit has been accepted']);

        $this->paymentDetailsResponseBuilderService = new PaymentDetailsResponseBuilderService(
            new PaymentStatusMapperService(),
            $orderProviderServiceMock,
            $stateTranslatorService
        );
    }

    public function testBuildPaymentDetailsResponseWithAuthorizationTransaction(): void
    {
        $paymentDetailsResponse = $this->createPaymentDetailsResponse();

        $paymentActionsResponse = new PaymentActionsResponseStruct();
        $paymentActionsResponse->addPaymentAction($this->createPaymentAuthorizationAction());

        $buildPaymentDetailsResponse = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse);

        $this->assertBasicPaymentDetails($paymentDetailsResponse, $buildPaymentDetailsResponse);

        static::assertCount(1, $buildPaymentDetailsResponse['transactions']);
        static::assertSame($paymentActionsResponse->toArray(), $buildPaymentDetailsResponse['transactions']);

        static::assertTrue($buildPaymentDetailsResponse['isCapturePossible']);
        static::assertTrue($buildPaymentDetailsResponse['isVoidPossible']);
        static::assertFalse($buildPaymentDetailsResponse['isRefundPossible']);
    }

    public function testBuildPaymentDetailsResponseWithCaptureTransaction(): void
    {
        $paymentDetailsResponse = $this->createPaymentDetailsResponse();
        $paymentActionsResponse = new PaymentActionsResponseStruct();

        $paymentActionsResponse->addPaymentAction($this->createPaymentAuthorizationAction());
        $paymentActionsResponse->addPaymentAction($this->createPaymentCaptureAction(100.00));

        $buildPaymentDetailsResponse = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse);

        static::assertSame($paymentDetailsResponse->getReference(), $buildPaymentDetailsResponse['transactionId']);
        static::assertSame($paymentDetailsResponse->getPaymentId(), $buildPaymentDetailsResponse['paymentId']);
        static::assertSame($paymentDetailsResponse->getAmount(), $buildPaymentDetailsResponse['totalAmount']);
        static::assertSame($paymentDetailsResponse->getCurrency(), $buildPaymentDetailsResponse['currency']);
        static::assertSame(100.00, $buildPaymentDetailsResponse['remainingRefundAmount']);

        static::assertCount(2, $buildPaymentDetailsResponse['transactions']);
        static::assertSame($paymentActionsResponse->toArray(), $buildPaymentDetailsResponse['transactions']);

        static::assertFalse($buildPaymentDetailsResponse['isCapturePossible']);
        static::assertFalse($buildPaymentDetailsResponse['isVoidPossible']);
        static::assertTrue($buildPaymentDetailsResponse['isRefundPossible']);
    }

    public function testBuildPaymentDetailsResponseWithPartialCaptureTransaction(): void
    {
        $paymentDetailsResponse = $this->createPaymentDetailsResponse();
        $paymentActionsResponse = new PaymentActionsResponseStruct();

        $paymentActionsResponse->addPaymentAction($this->createPaymentAuthorizationAction());
        $paymentActionsResponse->addPaymentAction($this->createPaymentCaptureAction(90.00));

        $buildPaymentDetailsResponse = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse);

        static::assertSame($paymentDetailsResponse->getReference(), $buildPaymentDetailsResponse['transactionId']);
        static::assertSame($paymentDetailsResponse->getPaymentId(), $buildPaymentDetailsResponse['paymentId']);
        static::assertSame($paymentDetailsResponse->getAmount(), $buildPaymentDetailsResponse['totalAmount']);
        static::assertSame($paymentDetailsResponse->getCurrency(), $buildPaymentDetailsResponse['currency']);
        static::assertSame(90.00, $buildPaymentDetailsResponse['remainingRefundAmount']);

        static::assertCount(2, $buildPaymentDetailsResponse['transactions']);
        static::assertSame($paymentActionsResponse->toArray(), $buildPaymentDetailsResponse['transactions']);

        static::assertFalse($buildPaymentDetailsResponse['isCapturePossible']);
        static::assertFalse($buildPaymentDetailsResponse['isVoidPossible']);
        static::assertTrue($buildPaymentDetailsResponse['isRefundPossible']);
    }

    public function testBuildPaymentDetailsResponseWithOnePartialRefundTransaction(): void
    {
        $paymentDetailsResponse = $this->createPaymentDetailsResponse();
        $paymentActionsResponse = new PaymentActionsResponseStruct();

        $paymentActionsResponse->addPaymentAction($this->createPaymentAuthorizationAction());
        $paymentActionsResponse->addPaymentAction($this->createPaymentCaptureAction(100.00));
        $paymentActionsResponse->addPaymentAction($this->createPaymentRefundAction(70.00));

        $buildPaymentDetailsResponse = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse);

        static::assertSame($paymentDetailsResponse->getReference(), $buildPaymentDetailsResponse['transactionId']);
        static::assertSame($paymentDetailsResponse->getPaymentId(), $buildPaymentDetailsResponse['paymentId']);
        static::assertSame($paymentDetailsResponse->getAmount(), $buildPaymentDetailsResponse['totalAmount']);
        static::assertSame($paymentDetailsResponse->getCurrency(), $buildPaymentDetailsResponse['currency']);
        static::assertSame(30.00, $buildPaymentDetailsResponse['remainingRefundAmount']);

        static::assertCount(3, $buildPaymentDetailsResponse['transactions']);
        static::assertSame($paymentActionsResponse->toArray(), $buildPaymentDetailsResponse['transactions']);

        static::assertFalse($buildPaymentDetailsResponse['isCapturePossible']);
        static::assertFalse($buildPaymentDetailsResponse['isVoidPossible']);
        static::assertTrue($buildPaymentDetailsResponse['isRefundPossible']);
    }

    public function testBuildPaymentDetailsResponseWithMultiplePartialRefundTransactions(): void
    {
        $paymentDetailsResponse = $this->createPaymentDetailsResponse();
        $paymentActionsResponse = new PaymentActionsResponseStruct();

        $paymentActionsResponse->addPaymentAction($this->createPaymentAuthorizationAction());
        $paymentActionsResponse->addPaymentAction($this->createPaymentCaptureAction(100.00));
        $paymentActionsResponse->addPaymentAction($this->createPaymentRefundAction(70.00));
        $paymentActionsResponse->addPaymentAction($this->createPaymentRefundAction(20.00));

        $buildPaymentDetailsResponse = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse);

        static::assertSame($paymentDetailsResponse->getReference(), $buildPaymentDetailsResponse['transactionId']);
        static::assertSame($paymentDetailsResponse->getPaymentId(), $buildPaymentDetailsResponse['paymentId']);
        static::assertSame($paymentDetailsResponse->getAmount(), $buildPaymentDetailsResponse['totalAmount']);
        static::assertSame($paymentDetailsResponse->getCurrency(), $buildPaymentDetailsResponse['currency']);
        static::assertSame(10.00, $buildPaymentDetailsResponse['remainingRefundAmount']);

        static::assertCount(4, $buildPaymentDetailsResponse['transactions']);
        static::assertSame($paymentActionsResponse->toArray(), $buildPaymentDetailsResponse['transactions']);

        static::assertFalse($buildPaymentDetailsResponse['isCapturePossible']);
        static::assertFalse($buildPaymentDetailsResponse['isVoidPossible']);
        static::assertTrue($buildPaymentDetailsResponse['isRefundPossible']);
    }

    public function testBuildPaymentDetailsResponseWithFullRefundTransaction(): void
    {
        $paymentDetailsResponse = $this->createPaymentDetailsResponse();
        $paymentActionsResponse = new PaymentActionsResponseStruct();

        $paymentActionsResponse->addPaymentAction($this->createPaymentAuthorizationAction());
        $paymentActionsResponse->addPaymentAction($this->createPaymentCaptureAction(100.00));
        $paymentActionsResponse->addPaymentAction($this->createPaymentRefundAction(100.00));

        $buildPaymentDetailsResponse = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse);

        $this->assertBasicPaymentDetails($paymentDetailsResponse, $buildPaymentDetailsResponse);

        static::assertCount(3, $buildPaymentDetailsResponse['transactions']);
        static::assertSame($paymentActionsResponse->toArray(), $buildPaymentDetailsResponse['transactions']);

        static::assertFalse($buildPaymentDetailsResponse['isCapturePossible']);
        static::assertFalse($buildPaymentDetailsResponse['isVoidPossible']);
        static::assertFalse($buildPaymentDetailsResponse['isRefundPossible']);
    }

    public function testBuildPaymentDetailsResponseWithVoidTransaction(): void
    {
        $paymentDetailsResponse = $this->createPaymentDetailsResponse();
        $paymentActionsResponse = new PaymentActionsResponseStruct();

        $paymentActionsResponse->addPaymentAction($this->createPaymentAuthorizationAction());
        $paymentActionsResponse->addPaymentAction($this->createPaymentVoidAction());

        $buildPaymentDetailsResponse = $this->paymentDetailsResponseBuilderService->buildPaymentDetailsResponse($paymentDetailsResponse, $paymentActionsResponse);

        $this->assertBasicPaymentDetails($paymentDetailsResponse, $buildPaymentDetailsResponse);

        static::assertCount(2, $buildPaymentDetailsResponse['transactions']);
        static::assertSame($paymentActionsResponse->toArray(), $buildPaymentDetailsResponse['transactions']);

        static::assertFalse($buildPaymentDetailsResponse['isCapturePossible']);
        static::assertFalse($buildPaymentDetailsResponse['isVoidPossible']);
        static::assertFalse($buildPaymentDetailsResponse['isRefundPossible']);
    }

    private function assertBasicPaymentDetails(
        PaymentDetailsResponseStruct $paymentDetailsResponse,
        array $buildPaymentDetailsResponse
    ): void
    {
        static::assertSame($paymentDetailsResponse->getReference(), $buildPaymentDetailsResponse['transactionId']);
        static::assertSame($paymentDetailsResponse->getPaymentId(), $buildPaymentDetailsResponse['paymentId']);
        static::assertSame($paymentDetailsResponse->getAmount(), $buildPaymentDetailsResponse['totalAmount']);
        static::assertSame($paymentDetailsResponse->getCurrency(), $buildPaymentDetailsResponse['currency']);
        static::assertSame(0.00, $buildPaymentDetailsResponse['remainingRefundAmount']);
    }

    private function createPaymentDetailsResponse(): PaymentDetailsResponseStruct
    {
        return new PaymentDetailsResponseStruct(
            'pay_hfdhfdhfdfdhfd',
            'abc',
            new \DateTimeImmutable(),
            'EUR',
            100.00,
            'Authorized',
            null,
            true
        );
    }

    private function createPaymentAuthorizationAction(): PaymentActionStruct
    {
        $paymentAction = new PaymentActionStruct();
        $paymentAction->setId('act_fdfdfddffff');
        $paymentAction->setType('authorization');
        $paymentAction->setDate(new \DateTimeImmutable('2020-09-17 07:00:00'));
        $paymentAction->setAmount(100.00);
        $paymentAction->setIsApproved(true);
        $paymentAction->setReference('abc');

        return $paymentAction;
    }

    private function createPaymentCaptureAction(float $amount): PaymentActionStruct
    {
        $paymentAction = new PaymentActionStruct();
        $paymentAction->setId('act_fgfgfggfgg');
        $paymentAction->setType(PaymentActionStruct::TYPE_CAPTURE);
        $paymentAction->setDate(new \DateTimeImmutable('2020-09-17 07:05:00'));
        $paymentAction->setAmount($amount);
        $paymentAction->setIsApproved(true);
        $paymentAction->setReference('abc');

        return $paymentAction;
    }

    private function createPaymentRefundAction(float $amount): PaymentActionStruct
    {
        $paymentAction = new PaymentActionStruct();
        $paymentAction->setId('act_uzhdfhfdjfd');
        $paymentAction->setType(PaymentActionStruct::TYPE_REFUND);
        $paymentAction->setDate(new \DateTimeImmutable('2020-09-17 07:08:00'));
        $paymentAction->setAmount($amount);
        $paymentAction->setIsApproved(true);
        $paymentAction->setReference('abc');

        return $paymentAction;
    }

    private function createPaymentVoidAction(): PaymentActionStruct
    {
        $paymentAction = new PaymentActionStruct();
        $paymentAction->setId('act_cbdgdteuw');
        $paymentAction->setType(PaymentActionStruct::TYPE_VOID);
        $paymentAction->setDate(new \DateTimeImmutable('2020-09-17 07:10:00'));
        $paymentAction->setIsApproved(true);
        $paymentAction->setReference('abc');

        return $paymentAction;
    }
}
