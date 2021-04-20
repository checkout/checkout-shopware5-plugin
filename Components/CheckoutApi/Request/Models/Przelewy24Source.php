<?php

declare(strict_types=1);

namespace CkoCheckoutPayment\Components\CheckoutApi\Request\Models;

use Checkout\Models\Payments\Source;

class Przelewy24Source extends Source
{

	/**
	 * Qualified name of the class.
	 *
	 * @var string
	 */
	const QUALIFIED_NAME = __CLASS__;

	/**
	 * Name of the model.
	 *
	 * @var string
	 */
	const MODEL_NAME = 'p24';

	/**
	 * Magic Methods
	 */

	/**
	 * Initialise source.
	 * @param string $description
	 */
	public function __construct($description, $account_holder_name, $account_holder_email, $payment_country)
	{
		$this->type = static::MODEL_NAME;
		$this->description = $description;
		$this->account_holder_name = $account_holder_name;
		$this->account_holder_email = $account_holder_email;
		$this->payment_country = $payment_country;
	}
}
