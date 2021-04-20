// {block name="backend/cko_checkout_payment/model/transaction"}
Ext.define('Shopware.apps.CkoCheckoutPayment.model.Transaction', {

    /**
     * @type { String }
     */
    extend: 'Ext.data.Model',

    /**
     * @type { Array }
     */
    fields: [
        // {block name="backend/cko_checkout_payment/model/transaction/fields"}{/block}
        { name: 'id', type: 'string' },
        { name: 'type', type: 'string' },
        { name: 'date', type: 'string' },
        { name: 'amount', type: 'string' },
        { name: 'isApproved', type: 'bool' },
    ],
});
// {/block}