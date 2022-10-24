<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks;

use CkoCheckoutPayment\Models\Configuration\CreditCardConfiguration;

class CreditCardConfigurationMock extends CreditCardConfiguration
{
    public function isThreeDsEnabled(): bool
    {
        return false;
    }

    public function isN3dAttemptEnabled(): bool
    {
        return false;
    }

    public function isDynamicBillingDescriptorEnabled(): bool
    {
        return false;
    }

    public function isSaveCardOptionEnabled(): bool
    {
        return false;
    }
}