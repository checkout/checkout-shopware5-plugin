{block name="frontend_checkout_payment_cko_checkout_payment_apple_pay_fieldset_description"}
    <div class="method--description is--last">
        {if $payment_mean.id == $ckoCurrentPaymentMethodId}
            {block name="frontend_checkout_payment_cko_checkout_payment_apple_pay_fieldset_description_not_available_communication_message"}
                <div class="cko-apple-pay-not-available-communication-message is--hidden">
                    {include file="frontend/_includes/messages.tpl" type="error" content="{s name="notAvailableCommunicationMessage"}{/s}"}
                </div>
            {/block}

            {* Apple Pay Payment Form *}
            {include file="frontend/cko_checkout_payment/cko_applepay/container.tpl"}
        {else}
            <img src="{link file='frontend/_public/src/img/applepay.svg'}" alt="{$payment_mean.description}" class="cko-checkout-payment-method">
        {/if}
    </div>
{/block}