<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentSession;

use CkoCheckoutPayment\Components\RequestConstants;

class PaymentSessionService implements PaymentSessionServiceInterface
{
    private const FAILED_SESSION = 'ckoCheckoutPaymentHasPaymentFailed';

    private const RESPONSE_CODE = 'ckoResponseCode';

    private const PAYMENT_REFERENCE = 'ckoPaymentReference';
    private const MANDATE_REFERENCE = 'ckoCheckoutPaymentMandateReference';

    private const SW_ORDER_VARIABLES = 'sOrderVariables';

    /**
     * @var \Enlight_Components_Session_Namespace
     */
    private $sessionManager;

    public function __construct(\Enlight_Components_Session_Namespace $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    public function set(string $key, string $value): void
    {
        $this->sessionManager->offsetSet($key, $value);
    }

    public function setPaymentReference(string $reference): void
    {
        $this->set(self::PAYMENT_REFERENCE, $reference);
    }

    public function setMandateReference(string $mandateReference): void
    {
        $this->set(self::MANDATE_REFERENCE, $mandateReference);
    }

    public function setPaymentFailed(bool $hasFailed = true): void
    {
        $this->sessionManager->offsetSet(self::FAILED_SESSION, $hasFailed);
    }

    public function setResponseCode(string $code): void
    {
        $this->sessionManager->offsetSet(self::RESPONSE_CODE, $code);
    }

    public function get(string $key): string
    {
        return (string) $this->sessionManager->offsetGet($key);
    }

    public function clearPaymentSession(): void
    {
        $this->sessionManager->offsetUnset(self::FAILED_SESSION);
        $this->sessionManager->offsetUnset(self::RESPONSE_CODE);
        $this->sessionManager->offsetUnset(self::PAYMENT_REFERENCE);
        $this->sessionManager->offsetUnset(self::MANDATE_REFERENCE);

        foreach (RequestConstants::getConstants() as $requestConstant) {
            if ($this->sessionManager->offsetExists($requestConstant)) {
                $this->sessionManager->offsetUnset($requestConstant);
            }
        }
    }

    public function hasPaymentFailed(): bool
    {
        return (bool) $this->sessionManager->offsetGet(self::FAILED_SESSION);
    }

    public function getResponseCode(): string
    {
        return $this->get(self::RESPONSE_CODE);
    }

    public function getPaymentReference(): string
    {
        return $this->get(self::PAYMENT_REFERENCE);
    }

    public function getMandateReference(): ?string
    {
        return $this->get(self::MANDATE_REFERENCE);
    }

    public function getOrderVariables(): \ArrayObject
    {
        return $this->sessionManager->offsetGet(self::SW_ORDER_VARIABLES);
    }
}
