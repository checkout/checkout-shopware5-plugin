<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\PaymentMethods;

class KlarnaPaymentMethod implements PaymentMethodInterface
{
	public const NAME = 'cko_klarna';

	public function getName(): string
	{
		return self::NAME;
	}

	public function getDescription(): string
	{
		return 'Klarna';
	}

	public function getAdditionalDescription(): string
	{
		return 'Klarna payment';
	}

	public function getAction(): string
	{
		return 'CkoCheckoutPayment';
	}

	public function getPosition(): int
	{
		return 0;
	}

	public function isActive(): bool
	{
		return false;
	}
}
