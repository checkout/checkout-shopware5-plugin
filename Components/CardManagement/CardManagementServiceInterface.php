<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CardManagement;

use CkoCheckoutPayment\Components\Structs\CardStruct;

interface CardManagementServiceInterface
{
    public function saveCard(CardStruct $cardStruct): void;

    public function getCards($customerId): array;

    public function deleteCard(int $customerId, $sourceId): void;
}
