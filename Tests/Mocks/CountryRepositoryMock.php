<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Tests\Mocks;

use Shopware\Models\Country\Country;
use Shopware\Models\Country\Repository as CountryRepository;

class CountryRepositoryMock extends CountryRepository
{
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $country = new Country();
        $country->setIso('DE');
        $country->setIso3('DEU');
        $country->setActive(true);

        return $country;
    }
}