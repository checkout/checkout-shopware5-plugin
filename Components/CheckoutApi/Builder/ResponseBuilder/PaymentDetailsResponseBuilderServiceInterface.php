<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Builder\ResponseBuilder;

use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentActionsResponseStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentDetailsResponseStruct;

interface PaymentDetailsResponseBuilderServiceInterface
{
    public const TRANSACTION_ID = 'transactionId';
    public const PAYMENT_ID = 'paymentId';
    public const TOTAL_AMOUNT = 'totalAmount';
    public const REMAINING_REFUND_AMOUNT = 'remainingRefundAmount';
    public const SEPA_MANDATE_REFERENCE = 'sepaMandateReference';
    public const CURRENCY = 'currency';
    public const STATUS = 'status';
    public const TRANSACTIONS = 'transactions';

    public const IS_CAPTURE_POSSIBLE = 'isCapturePossible';
    public const IS_VOID_POSSIBLE = 'isVoidPossible';
    public const IS_REFUND_POSSIBLE = 'isRefundPossible';

    public function buildPaymentDetailsResponse(
        PaymentDetailsResponseStruct $paymentDetailsResponse,
        PaymentActionsResponseStruct $paymentActionsResponse,
        ?string $sepaMandateReference = null
    ): array;
}
