<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Builder\ResponseBuilder;

use CkoCheckoutPayment\Components\CheckoutApi\CheckoutApiPaymentStatus;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentActionStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentActionsResponseStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentDetailsResponseStruct;
use CkoCheckoutPayment\Components\OrderProvider\OrderProviderServiceInterface;
use CkoCheckoutPayment\Components\PaymentStatusMapper\PaymentStatusMapperServiceInterface;
use Shopware\Components\StateTranslatorService;
use Shopware\Components\StateTranslatorServiceInterface;

class PaymentDetailsResponseBuilderService implements PaymentDetailsResponseBuilderServiceInterface
{
    /**
     * @var PaymentStatusMapperServiceInterface
     */
    private $paymentStatusMapperService;

    /**
     * @var OrderProviderServiceInterface
     */
    private $orderProviderService;

    /**
     * @var StateTranslatorServiceInterface
     */
    private $stateTranslatorService;

    public function __construct(
        PaymentStatusMapperServiceInterface $paymentStatusMapperService,
        OrderProviderServiceInterface $orderProviderService,
        StateTranslatorServiceInterface $stateTranslatorService
    ) {
        $this->paymentStatusMapperService = $paymentStatusMapperService;
        $this->orderProviderService = $orderProviderService;
        $this->stateTranslatorService = $stateTranslatorService;
    }

    public function buildPaymentDetailsResponse(
        PaymentDetailsResponseStruct $paymentDetailsResponse,
        PaymentActionsResponseStruct $paymentActionsResponse,
        ?string $sepaMandateReference = null
    ): array {
        $paymentStatus = $this->orderProviderService->getOrderStatusById($this->paymentStatusMapperService->mapStatus($paymentDetailsResponse->getStatus()));
        $paymentStatusName = $this->stateTranslatorService->translateState(StateTranslatorService::STATE_PAYMENT, ['name' => $paymentStatus])['description'] ?? $paymentStatus;

        return [
            self::TRANSACTION_ID => $paymentDetailsResponse->getReference(),
            self::PAYMENT_ID => $paymentDetailsResponse->getPaymentId(),
            self::TOTAL_AMOUNT => $paymentDetailsResponse->getAmount(),
            self::REMAINING_REFUND_AMOUNT => $this->getRemainingRefundAmount($paymentActionsResponse),
            self::SEPA_MANDATE_REFERENCE => $sepaMandateReference,
            self::CURRENCY => $paymentDetailsResponse->getCurrency(),
            self::STATUS => $paymentStatusName,
            self::TRANSACTIONS => $paymentActionsResponse->toArray(),
            self::IS_CAPTURE_POSSIBLE => $this->isCapturePossible($paymentDetailsResponse, $paymentActionsResponse),
            self::IS_VOID_POSSIBLE => $this->isVoidPossible($paymentDetailsResponse, $paymentActionsResponse),
            self::IS_REFUND_POSSIBLE => $this->isRefundPossible($paymentDetailsResponse, $paymentActionsResponse)
        ];
    }

    private function isCapturePossible(PaymentDetailsResponseStruct $paymentDetailsResponse, PaymentActionsResponseStruct $paymentActionsResponse): bool
    {
        if ($this->isPaymentPending($paymentDetailsResponse)) {
            return false;
        }

        return !$this->isPaymentCaptured($paymentActionsResponse) && !$this->isPaymentVoided($paymentActionsResponse);
    }

    private function isVoidPossible(PaymentDetailsResponseStruct $paymentDetailsResponse, PaymentActionsResponseStruct $paymentActionsResponse): bool
    {
        if ($this->isPaymentPending($paymentDetailsResponse)) {
            return false;
        }

        return !$this->isPaymentVoided($paymentActionsResponse) && !$this->isPaymentCaptured($paymentActionsResponse);
    }

    private function isRefundPossible(PaymentDetailsResponseStruct $paymentDetailsResponse, PaymentActionsResponseStruct $paymentActionsResponse): bool
    {
        if ($this->isPaymentPending($paymentDetailsResponse)) {
            return false;
        }

        return $this->getRemainingRefundAmount($paymentActionsResponse) > 0 && $this->isPaymentCaptured($paymentActionsResponse);
    }

    private function isPaymentCaptured(PaymentActionsResponseStruct $paymentActionsResponse): bool
    {
        $captureTransactions = $this->getFilteredPaymentActionsByType(PaymentActionStruct::TYPE_CAPTURE, $paymentActionsResponse);

        return count($captureTransactions) > 0;
    }

    private function isPaymentVoided(PaymentActionsResponseStruct $paymentActionsResponse): bool
    {
        $voidTransactions = $this->getFilteredPaymentActionsByType(PaymentActionStruct::TYPE_VOID, $paymentActionsResponse);

        return count($voidTransactions) > 0;
    }

    private function isPaymentPending(PaymentDetailsResponseStruct $paymentDetailsResponse): bool
    {
        return $paymentDetailsResponse->getStatus() === CheckoutApiPaymentStatus::API_PAYMENT_PENDING;
    }

    private function getTotalTransactionsAmount(string $type, PaymentActionsResponseStruct $paymentActionsResponse): float
    {
        $totalTransactionsAmount = 0.0;
        foreach ($this->getFilteredPaymentActionsByType($type, $paymentActionsResponse) as $transaction) {
            $totalTransactionsAmount += $transaction->getAmount();
        }

        return $totalTransactionsAmount;
    }

    private function getRemainingRefundAmount(PaymentActionsResponseStruct $paymentActionsResponse): float
    {
        $totalCaptureTransactionsAmount = $this->getTotalTransactionsAmount(PaymentActionStruct::TYPE_CAPTURE, $paymentActionsResponse);
        $totalRefundTransactionsAmount = $this->getTotalTransactionsAmount(PaymentActionStruct::TYPE_REFUND, $paymentActionsResponse);

        return $totalCaptureTransactionsAmount - $totalRefundTransactionsAmount;
    }

    private function getFilteredPaymentActionsByType(string $type, PaymentActionsResponseStruct $paymentActionsResponse): array
    {
        return array_filter($paymentActionsResponse->getPaymentActions(), function (PaymentActionStruct $paymentAction) use ($type): bool {
            return strtolower($paymentAction->getType()) === strtolower($type);
        });
    }
}
