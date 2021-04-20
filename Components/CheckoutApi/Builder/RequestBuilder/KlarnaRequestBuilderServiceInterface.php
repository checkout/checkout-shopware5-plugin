<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Builder\RequestBuilder;

use Checkout\Models\Address;
use Checkout\Models\Payments\KlarnaSource;
use CkoCheckoutPayment\Components\CheckoutApi\Structs\KlarnaRequestDataStruct;

interface KlarnaRequestBuilderServiceInterface
{
	public function createKlarnaPaymentInitializeData(array $basket, array $user, ?int $shopId): KlarnaRequestDataStruct;

	public function createKlarnaSource(string $token, string $purchaseCountry, array $billingAddress, array $basket, array $userData): KlarnaSource;

    public function createBillingAddress(array $billingAddress, array $userData): Address;
}
