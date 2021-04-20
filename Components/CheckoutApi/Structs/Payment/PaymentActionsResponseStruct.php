<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment;

class PaymentActionsResponseStruct
{
    /**
     * @var PaymentActionStruct[]
     */
    private $paymentActions;

    /**
     * @return PaymentActionStruct[]
     */
    public function getPaymentActions(): array
    {
        return $this->paymentActions;
    }

    /**
     * @param PaymentActionStruct[] $paymentActions
     */
    public function setPaymentActions(array $paymentActions): void
    {
        $this->paymentActions = $paymentActions;
    }

    public function addPaymentAction(PaymentActionStruct $paymentAction): void
    {
        $this->paymentActions[] = $paymentAction;
    }

    public function toArray(): array
    {
        $paymentActions = [];
        foreach ($this->paymentActions as $paymentAction) {
            $paymentActions[] = $paymentAction->toArray();
        }

        return $paymentActions;
    }
}
