{block name="frontend_checkout_payment_cko_checkout_payment_giropay_fieldset_description"}
    <div class="method--description is--last">
        <img src="{link file='frontend/_public/src/img/giropay.svg'}" alt="{$payment_mean.description}" class="cko-checkout-payment-method">

        {if $payment_mean.id == $ckoCurrentPaymentMethodId}
            {* Giropay Payment Form *}
            {include file="frontend/cko_checkout_payment/cko_giropay/container.tpl"}
        {/if}
    </div>
{/block}