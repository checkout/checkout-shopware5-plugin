{* Google Pay Payment Method *}
{block name="frontend_checkout_payment_cko_checkout_payment_google_pay_fieldset_input_label"}
    {if $payment_mean.name == $googlePayPaymentMethodName}
        <div class="method--label is--first is--cko-google-pay-payment">
            <label class="method--name is--strong"
                   for="payment_mean{$payment_mean.id}">{$payment_mean.description}</label>
        </div>
    {/if}
{/block}