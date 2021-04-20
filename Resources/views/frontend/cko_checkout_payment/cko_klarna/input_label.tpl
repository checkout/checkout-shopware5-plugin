{* Klarna Payment Method *}
{block name="frontend_checkout_payment_cko_checkout_payment_klarna_fieldset_input_label"}
    {if $payment_mean.name == $klarnaPaymentMethodName}
        <div class="method--label is--first is--cko-klarna-pay-payment">
            <label class="method--name is--strong"
                   for="payment_mean{$payment_mean.id}">{$payment_mean.description}</label>
        </div>
    {/if}
{/block}