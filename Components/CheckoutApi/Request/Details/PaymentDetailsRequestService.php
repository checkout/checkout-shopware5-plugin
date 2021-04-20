<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Details;

use Checkout\Library\Exceptions\CheckoutException;
use Checkout\Models\Payments\Payment;
use CkoCheckoutPayment\Components\CheckoutApi\AbstractCheckoutPaymentService;
use CkoCheckoutPayment\Components\CheckoutApi\Exception\CheckoutApiRequestException;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\Payment\PaymentDetailsResponseStruct;

class PaymentDetailsRequestService extends AbstractCheckoutPaymentService implements PaymentDetailsRequestServiceInterface
{
    private const PLANNED_DEBIT_DATE_INTERVAL = 'P3D';

    public function getPaymentDetails(string $threeDsSessionId, ?int $shopId): PaymentDetailsResponseStruct
    {
        try {
            /** @var Payment $paymentDetails */
            $paymentDetails = $this->apiClientService->createClient($shopId)->payments()->details($threeDsSessionId);

            $paymentId = (string) $paymentDetails->getId();
            $status = (string) $paymentDetails->getValue('status');

            $paymentDetailsResponse = new PaymentDetailsResponseStruct(
                $paymentId,
                (string) $paymentDetails->getValue('reference'),
                $this->getPlannedDebitDate($paymentDetails),
                (string) $paymentDetails->getValue('currency'),
                $this->calculateAmount((float) $paymentDetails->getValue('amount'), self::CALCULATE_TYPE_DIVIDE),
                $status,
                $paymentDetails->getValue('source') ?: null,
                (bool) $paymentDetails->getValue('approved')
            );

            $this->loggerService->info(sprintf('Processed payment %s with status %s', $paymentId, $status));

            return $paymentDetailsResponse;
        } catch (CheckoutException $checkoutException) {
            throw new CheckoutApiRequestException($checkoutException->getMessage(), $checkoutException->getCode(), $checkoutException);
        }
    }

    private function getPlannedDebitDate(Payment $paymentDetails): \DateTimeImmutable
    {
        $paymentSource = $paymentDetails->getValue('source');

        try {
            if ($paymentSource && !empty($paymentSource['planned_debit_date'])) {
                return new \DateTimeImmutable($paymentSource['planned_debit_date']);
            }

            $plannedDebitDate = new \DateTimeImmutable($paymentDetails->getValue('requested_on'));

            return $plannedDebitDate->add(new \DateInterval(self::PLANNED_DEBIT_DATE_INTERVAL));
        } catch (\Throwable $e) {
            return new \DateTimeImmutable(self::PLANNED_DEBIT_DATE_INTERVAL);
        }
    }
}
