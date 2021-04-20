<?php


namespace CkoCheckoutPayment\Components\PaymentMethods;


class Przelewy24PaymentMethod implements PaymentMethodInterface
{
	public const NAME = 'cko_przelewy24';

	public function getName(): string
	{
		return self::NAME;
	}

	public function getDescription(): string
	{
		return 'Przelewy';
	}

	public function getAdditionalDescription(): string
	{
		return 'przelewy payment';
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
