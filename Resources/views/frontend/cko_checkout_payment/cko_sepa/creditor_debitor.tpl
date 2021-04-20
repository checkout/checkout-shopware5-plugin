{block name="frontend_checkout_payment_cko_checkout_payment_sepa_creditor_debitor"}
	{block name="frontend_checkout_payment_cko_checkout_payment_sepa_creditor_debitor_creditor"}
		<fieldset class="cko-sepa-creditor">
			<div><strong>{s name="creditor"}{/s}</strong></div>
			<div>{$ckoSepaMandateCreditorName}</div>
			<div>{s name="creditorId"}{/s} {$ckoSepaMandateCreditorId}</div>
			<div>{$ckoSepaMandateCreditorAddressFirst}</div>
			<div>{$ckoSepaMandateCreditorAddressSecond}</div>
			<div>{$ckoSepaMandateCreditorCountry}</div>
		</fieldset>
	{/block}

	{block name="frontend_checkout_payment_cko_checkout_payment_sepa_creditor_debitor_debitor"}
		<fieldset class="cko-sepa-debitor">
			<div><strong>{s name="debitor"}{/s}</strong></div>
			<div>{$ckoUserBillingAddress.firstname} {$ckoUserBillingAddress.lastname}</div>
			<div>{$ckoUserBillingAddress.street}</div>
			{if $ckoUserBillingAddress.additionalAddressLine1}
				<div>{$ckoUserBillingAddress.additionalAddressLine1}</div>
			{/if}
			<div>{$ckoUserBillingAddress.zipcode} {$ckoUserBillingAddress.city}</div>
			<div>{$ckoUserBillingCountryName}</div>
		</fieldset>
	{/block}
{/block}
