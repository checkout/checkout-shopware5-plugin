// {block name="backend/cko_checkout_payment/model/capture"}
Ext.define('Shopware.apps.CkoCheckoutPayment.model.Capture', {

    /**
     * @type { String }
     */
    extend: 'Ext.data.Model',

    /**
     * @type { Array }
     */
    fields: [
        // {block name="backend/cko_checkout_payment/model/capture/fields"}{/block}
        { name: 'captureAmount', type: 'float' },
        { name: 'isPartialCapture', type: 'bool' },
    ],
});
// {/block}