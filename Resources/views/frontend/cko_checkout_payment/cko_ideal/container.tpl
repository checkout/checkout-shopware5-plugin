{block name="frontend_checkout_payment_cko_checkout_payment_ideal_container"}
    <div data-cko-checkout-payment-ideal="true"
         data-cko-checkout-payment-ideal-request-url="{url controller=CkoCheckoutPaymentIdeal action=savePaymentData module=frontend}">
        {block name="frontend_checkout_payment_cko_checkout_payment_ideal_container_bic_input_field"}
            <input name="ckoBic"
                   type="text"
                   required="required"
                   aria-required="true"
                   autocomplete="false"
                   placeholder="{s name="input/bic/placeholder"}{/s}"
                   id="cko-ideal-bic-field">
        {/block}
    </div>
{/block}