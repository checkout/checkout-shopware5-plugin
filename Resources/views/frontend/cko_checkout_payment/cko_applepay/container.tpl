{block name="frontend_checkout_payment_cko_checkout_payment_apple_pay_container"}
    <div data-cko-checkout-payment-apple-pay="true"
         data-cko-checkout-payment-apple-pay-request-url="{url controller=CkoCheckoutPaymentApplePay action=savePaymentData module=frontend}"
         data-cko-checkout-payment-apple-pay-merchant-validation-request-url="{url controller=CkoCheckoutPaymentApplePay action=validateMerchant module=frontend}"
         data-cko-checkout-payment-apple-pay-merchant-id="{$ckoApplePayMerchantId}"
         data-cko-checkout-payment-apple-pay-supported-networks="{$ckoApplePaySupportedNetworks}"
         data-cko-checkout-payment-apple-pay-merchant-capabilities="{$ckoApplePayMerchantCapabilities}"
         data-cko-checkout-payment-apple-pay-currency="{$ckoCurrentCurrency}"
         data-cko-checkout-payment-apple-pay-country-code="{$ckoUserBillingCountryCode}"
         data-cko-checkout-payment-apple-pay-shop-name="{$ckoShopName}"
         data-cko-checkout-payment-apple-pay-total-price="{$ckoTotalPrice}"
         class="checkout-payment-apple-pay-container">
        {block name="frontend_checkout_payment_cko_checkout_payment_apple_pay_container_pay_button"}
            <button type="button"
                    id="cko-apple-pay-button"
                    class="apple-pay-button apple-pay-button-{$ckoApplePayButtonColor} is--hidden"></button>
        {/block}
    </div>
{/block}