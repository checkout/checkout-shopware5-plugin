{block name="frontend_checkout_payment_cko_checkout_payment_google_pay_fieldset_description"}
    <div class="method--description is--last">
        {if $payment_mean.id == $ckoCurrentPaymentMethodId}
            {* Google Pay Payment Form *}
            {include file="frontend/cko_checkout_payment/cko_googlepay/container.tpl"}
        {else}
            <img src="{link file='frontend/_public/src/img/googlepay.svg'}" alt="{$payment_mean.description}" class="cko-checkout-payment-method">
        {/if}
    </div>
{/block}