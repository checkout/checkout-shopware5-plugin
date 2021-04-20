<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Structs;

class GooglePayStruct
{
    /**
     * @var string|null
     */
    private $signature;

    /**
     * @var string|null
     */
    private $protocolVersion;

    /**
     * @var string|null
     */
    private $signedMessage;

    public function __construct(
        ?string $signature,
        ?string $protocolVersion,
        ?string $signedMessage
    ) {
        $this->signature = $signature;
        $this->protocolVersion = $protocolVersion;
        $this->signedMessage = $signedMessage;
    }

    public function getSignature(): ?string
    {
        return $this->signature;
    }

    public function setSignature(?string $signature): void
    {
        $this->signature = $signature;
    }

    public function getProtocolVersion(): ?string
    {
        return $this->protocolVersion;
    }

    public function setProtocolVersion(?string $protocolVersion): void
    {
        $this->protocolVersion = $protocolVersion;
    }

    public function getSignedMessage(): ?string
    {
        return $this->signedMessage;
    }

    public function setSignedMessage(?string $signedMessage): void
    {
        $this->signedMessage = $signedMessage;
    }
}