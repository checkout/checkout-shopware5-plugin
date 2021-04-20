// {block name="backend/cko_setup/model/paypal/configuration"}
Ext.define('Shopware.apps.CkoSetup.model.Paypal.Configuration', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'CkoSetupPayPal'
        };
    },

    fields: [
        // {block name="backend/cko_setup/model/paypal/configuration/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'shopId', type: 'int' },
        { name: 'autoCaptureEnabled', type: 'bool' }
    ]
});
// {/block}