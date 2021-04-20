{block name="frontend_checkout_payment_cko_checkout_payment_ideal_fieldset_description"}
    <div class="method--description is--last">
        <img src="{link file='frontend/_public/src/img/ideal.svg'}" alt="{$payment_mean.description}" class="cko-checkout-payment-method">

        {if $payment_mean.id == $ckoCurrentPaymentMethodId}
            {* Ideal Payment Form *}
            {include file="frontend/cko_checkout_payment/cko_ideal/container.tpl"}
        {/if}
    </div>
{/block}