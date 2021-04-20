{block name="frontend_checkout_payment_cko_checkout_payment_google_pay_container"}
    <div data-cko-checkout-payment-google-pay="true"
         data-cko-checkout-payment-google-pay-request-url="{url controller=CkoCheckoutPaymentGooglePay action=savePaymentData module=frontend}"
         data-cko-checkout-payment-google-pay-allowed-card-networks="{$ckoGooglePayAllowedCardNetworks}"
         data-cko-checkout-payment-google-pay-merchant-id="{$ckoGooglePayMerchantId}"
         data-cko-checkout-payment-google-pay-gateway-merchant-id="{$ckoGooglePayGatewayMerchantId}"
         data-cko-checkout-payment-google-pay-environment="{$ckoGooglePayEnvironment}"
         data-cko-checkout-payment-google-pay-currency="{$ckoCurrentCurrency}"
         data-cko-checkout-payment-google-pay-total-price="{$ckoTotalPrice}"
         class="checkout-payment-google-pay-container">
        {block name="frontend_checkout_payment_cko_checkout_payment_google_pay_container_pay_button"}
            <button type="button"
                    id="cko-google-pay-button"
                    class="google-pay google-pay-button-{$ckoGooglePayButtonColor} is--hidden"></button>
        {/block}
    </div>
{/block}