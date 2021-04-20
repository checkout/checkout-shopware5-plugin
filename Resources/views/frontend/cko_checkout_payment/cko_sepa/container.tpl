{block name="frontend_checkout_payment_cko_checkout_payment_sepa"}
    <div data-cko-checkout-payment-sepa="true"
		data-cko-checkout-payment-sepa-request-url="{url controller=CkoCheckoutPaymentSepa action=savePaymentData module=frontend}"
		class="cko-sepa-mandate-content">
        {include file="frontend/cko_checkout_payment/cko_sepa/creditor_debitor.tpl"}
        {include file="frontend/cko_checkout_payment/cko_sepa/input_fields.tpl"}
        {include file="frontend/cko_checkout_payment/cko_sepa/sepa_mandate.tpl"}
    </div>
{/block}
