// {block name="backend/cko_checkout_payment/model/refund"}
Ext.define('Shopware.apps.CkoCheckoutPayment.model.Refund', {

    /**
     * @type { String }
     */
    extend: 'Ext.data.Model',

    /**
     * @type { Array }
     */
    fields: [
        // {block name="backend/cko_checkout_payment/model/refund/fields"}{/block}
        { name: 'refundAmount', type: 'float' },
        { name: 'isPartialCapture', type: 'bool' },
    ],
});
// {/block}