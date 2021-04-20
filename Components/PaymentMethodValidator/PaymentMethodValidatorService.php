<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethodValidator;

use CkoCheckoutPayment\Components\CheckoutApi\Request\PaymentRequestServiceInterface;
use CkoCheckoutPayment\Components\PaymentSession\PaymentSessionServiceInterface;

class PaymentMethodValidatorService implements PaymentMethodValidatorServiceInterface
{
    private const CKO_PAYMENT_METHOD_PREFIX = 'cko_';

    /**
     * @var PaymentSessionServiceInterface
     */
    private $paymentSessionService;

    /**
     * @var PaymentRequestServiceInterface[]
     */
    private $paymentRequestServices;

    public function __construct(PaymentSessionServiceInterface $paymentSessionService)
    {
        $this->paymentSessionService = $paymentSessionService;
    }

    public function isCheckoutPaymentMethod(string $paymentMethodName): bool
    {
        return mb_substr($paymentMethodName, 0, 4) === self::CKO_PAYMENT_METHOD_PREFIX;
    }

    public function isPaymentMethodValid(string $paymentMethodName): bool
    {
        foreach ($this->paymentRequestServices as $paymentRequestService) {
            if ($paymentRequestService->supportsPaymentRequest($paymentMethodName)) {
                return $paymentRequestService->isPaymentSessionValid();
            }
        }

        return false;
    }

    public function addPaymentRequestService(PaymentRequestServiceInterface $paymentRequestService): void
    {
        $this->paymentRequestServices[] = $paymentRequestService;
    }
}
