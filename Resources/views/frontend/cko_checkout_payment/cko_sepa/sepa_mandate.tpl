{block name="frontend_checkout_payment_cko_checkout_payment_sepa_mandate_agreement"}
	<fieldset>
		{block name="frontend_checkout_payment_cko_checkout_payment_sepa_mandate_agreement_info_text_one"}
			<div>{$ckoSepaMandateAgreementTextOne}</div>
		{/block}

		{block name="frontend_checkout_payment_cko_checkout_payment_sepa_mandate_agreement_info_text_two"}
			<div>{s name="mandateAgreement/textTwo"}{/s}</div>
		{/block}
	</fieldset>
	<fieldset>
		<div class="cko-accept-mandate-agrement-checkbox-container">
				<div class="cko-sepa-mandate-agreement-checkbox-label">
					{block name="frontend_checkout_payment_cko_checkout_payment_sepa_mandate_agreement_checkbox"}
						<input name="ckoAcceptMandate"
						   type="checkbox"
						   required="required"
						   aria-required="true"
						   id="cko-sepa-accept-mandate-field">
					{/block}
					{block name="frontend_checkout_payment_cko_checkout_payment_sepa_mandate_agreement_label"}
						<label for="cko-sepa-accept-mandate-field" class="is--strong">{s name="mandateAgreement/checkbox/label"}{/s}</label>
					{/block}
				</div>
		</div>
	</fieldset>
{/block}
