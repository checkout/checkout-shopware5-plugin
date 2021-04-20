// {block name="backend/cko_checkout_payment/model/payment"}
Ext.define('Shopware.apps.CkoCheckoutPayment.model.Payment', {

    /**
     * @type { String }
     */
    extend: 'Ext.data.Model',

    /**
     * @type { Array }
     */
    fields: [
        // {block name="backend/cko_checkout_payment/model/payment/fields"}{/block}
        { name: 'transactionId', type: 'string' },
        { name: 'paymentId', type: 'string' },
        { name: 'sepaMandateReference', type: 'string' },
        { name: 'totalAmount', type: 'float' },
        { name: 'remainingRefundAmount', type: 'float' },
        { name: 'currency', type: 'string' },
        { name: 'status', type: 'string' },
        { name: 'isCapturePossible', type: 'bool' },
        { name: 'isVoidPossible', type: 'bool' },
        { name: 'isRefundPossible', type: 'bool' },
    ],

    hasMany: [
        // {block name="backend/cko_checkout_payment/model/payment/associations"}{/block}
        {
            name: 'transactions',
            associationKey: 'transactions',
            model: 'Shopware.apps.CkoCheckoutPayment.model.Transaction'
        }
    ]
});
// {/block}