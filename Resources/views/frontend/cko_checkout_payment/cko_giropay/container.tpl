{block name="frontend_checkout_payment_cko_checkout_payment_giropay_container"}
    <div data-cko-checkout-payment-giropay="true"
         data-cko-checkout-payment-giropay-request-url="{url controller=CkoCheckoutPaymentGiropay action=savePaymentData module=frontend}">
        {block name="frontend_checkout_payment_cko_checkout_payment_giropay_container_bic_input_field"}
            <input name="ckoBic"
                   type="text"
                   required="required"
                   aria-required="true"
                   autocomplete="false"
                   placeholder="{s name="input/bic/placeholder"}{/s}"
                   id="cko-giropay-bic-field">
        {/block}
    </div>
{/block}