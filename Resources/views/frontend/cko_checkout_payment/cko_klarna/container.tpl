{block name="frontend_checkout_payment_cko_checkout_payment_klarna_container"}
    <div data-cko-checkout-payment-klarna="true"
         data-cko-checkout-payment-klarna-client-token="{$ckoKlarnaClientToken}"
         data-cko-checkout-payment-klarna-instance-id="{$ckoKlarnaInstanceId}"
         data-cko-checkout-payment-klarna-payment-methods="{$ckoKlarnaPaymentMethods|json_encode|escape:"html"}"
         data-cko-checkout-payment-klarna-data="{$ckoKlarnaData|json_encode|escape:"html"}"
         data-cko-checkout-payment-klarna-request-url="{url controller=CkoCheckoutPaymentKlarna action=savePaymentData module=frontend}"
         data-cko-checkout-payment-klarna-check-signature-url="{url controller=CkoCheckoutPaymentKlarna action=checkBasketSignature module=frontend}">
    </div>
    <div id="klarna_container"></div>
{/block}