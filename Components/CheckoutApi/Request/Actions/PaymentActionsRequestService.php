<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Actions;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Response;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentActionsResponseStruct;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentActionStruct;

class PaymentActionsRequestService extends AbstractCheckoutPaymentService implements PaymentActionsRequestServiceInterface
{
    public function getPaymentActions(string $paymentId, int $shopId): PaymentActionsResponseStruct
    {
        try {
            return $this->createPaymentActionsFromResponse($this->apiClientService->createClient($shopId)->payments()->actions($paymentId));
        } catch (CheckoutException $checkoutException) {
            $this->loggerService->error($checkoutException->getMessage(), ['exception' => $checkoutException]);

            $paymentActionsResponse = new PaymentActionsResponseStruct();
            $paymentActionsResponse->setPaymentActions([]);

            return $paymentActionsResponse;
        }
    }

    private function createPaymentActionsFromResponse(Response $response): PaymentActionsResponseStruct
    {
        $paymentActionsResponse = new PaymentActionsResponseStruct();

        foreach ($response->getValue('list') as $paymentActionList) {
            $paymentAction = new PaymentActionStruct();
            $paymentAction->setId((string) $paymentActionList->getValue('id'));
            $paymentAction->setType(mb_strtolower($paymentActionList->getValue('type')));
            $paymentAction->setDate(new \DateTimeImmutable($paymentActionList->getValue('processed_on')));
            $paymentAction->setAmount($this->calculateAmount((float) $paymentActionList->getValue('amount'), self::CALCULATE_TYPE_DIVIDE));
            $paymentAction->setIsApproved((bool) $paymentActionList->getValue('approved'));
            $paymentAction->setReference((string) $paymentActionList->getValue('reference'));

            $paymentActionsResponse->addPaymentAction($paymentAction);
        }

        return $paymentActionsResponse;
    }
}
