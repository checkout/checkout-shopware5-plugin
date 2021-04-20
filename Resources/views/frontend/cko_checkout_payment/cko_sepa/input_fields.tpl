<fieldset>
	{block name="frontend_checkout_payment_cko_checkout_payment_sepa_input_fields_iban_input_field"}
		<input name="ckoIban"
			   type="text"
			   required="required"
			   aria-required="true"
			   autocomplete="false"
			   placeholder="{s name="input/iban/placeholder"}{/s}"
			   id="cko-sepa-iban-field">
	{/block}

	{block name="frontend_checkout_payment_cko_checkout_payment_sepa_input_fields_bic_input_field"}
		<input name="ckoBic"
			   type="text"
			   autocomplete="false"
			   placeholder="{s name="input/bic/placeholder"}{/s}"
			   id="cko-sepa-bic-field">
	{/block}
</fieldset>
