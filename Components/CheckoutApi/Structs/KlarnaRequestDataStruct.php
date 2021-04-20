<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs;

class KlarnaRequestDataStruct
{
    /**
     * @var string
     */
    private $clientToken;

    /**
     * @var string
     */
    private $instanceId;

    /**
     * @var array
     */
    private $paymentMethods;

    /**
     * @var array
     */
    private $requestData;

    public function __construct(
        string $clientToken,
        string $instanceId,
        array $paymentMethods,
        array $requestData
    ) {
        $this->clientToken = $clientToken;
        $this->instanceId = $instanceId;
        $this->paymentMethods = $paymentMethods;
        $this->requestData = $requestData;
    }

    /**
     * @return string
     */
    public function getClientToken(): string
    {
        return $this->clientToken;
    }

    /**
     * @param string $clientToken
     */
    public function setClientToken(string $clientToken): void
    {
        $this->clientToken = $clientToken;
    }

    /**
     * @return string
     */
    public function getInstanceId(): string
    {
        return $this->instanceId;
    }

    /**
     * @param string $instanceId
     */
    public function setInstanceId(string $instanceId): void
    {
        $this->instanceId = $instanceId;
    }

    /**
     * @return array
     */
    public function getPaymentMethods(): array
    {
        return $this->paymentMethods;
    }

    /**
     * @param array $paymentMethods
     */
    public function setPaymentMethods(array $paymentMethods): void
    {
        $this->paymentMethods = $paymentMethods;
    }

    /**
     * @return array
     */
    public function getRequestData(): array
    {
        return $this->requestData;
    }

    /**
     * @param array $requestData
     */
    public function setRequestData(array $requestData): void
    {
        $this->requestData = $requestData;
    }
}