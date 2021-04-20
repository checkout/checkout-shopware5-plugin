{block name="frontend_checkout_payment_cko_checkout_payment_klarna_fieldset_description"}
    <div class="method--description is--last">
        <img src="{link file='frontend/_public/src/img/klarna.svg'}" alt="{$payment_mean.description}" class="cko-checkout-payment-method">

        {if $payment_mean.id == $ckoCurrentPaymentMethodId}
            {block name="frontend_checkout_payment_cko_checkout_payment_klarna_fieldset_description_not_available_communication_message"}
                <div class="cko-klarna-not-available-communication-message is--hidden">
                    {include file="frontend/_includes/messages.tpl" type="error" content="{s name="notAvailableCommunicationMessage"}{/s}"}
                </div>
            {/block}

            <br />
            {* Klarna Payment Form *}
            {include file="frontend/cko_checkout_payment/cko_klarna/container.tpl"}
        {/if}
    </div>
{/block}