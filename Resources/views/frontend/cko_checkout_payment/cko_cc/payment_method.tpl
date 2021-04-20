{block name="frontend_checkout_payment_cko_checkout_payment_credit_card_fieldset_description"}
    <div class="method--description is--last">
        <img src="{link file='frontend/_public/src/img/card-icons/card.svg'}" alt="{$payment_mean.description}" class="cko-checkout-payment-method">

        {if $payment_mean.id == $ckoCurrentPaymentMethodId}
            {* Credit Card Payment Form *}
            {include file="frontend/cko_checkout_payment/cko_cc/container.tpl"}
        {/if}
    </div>
{/block}