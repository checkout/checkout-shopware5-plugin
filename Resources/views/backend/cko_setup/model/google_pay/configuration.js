// {block name="backend/cko_setup/model/google_pay/configuration"}
Ext.define('Shopware.apps.CkoSetup.model.GooglePay.Configuration', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'CkoSetupGooglePay'
        };
    },

    fields: [
        // {block name="backend/cko_setup/model/google_pay/configuration/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'shopId', type: 'int' },
        { name: 'autoCaptureEnabled', type: 'bool' },
        { name: 'merchantId', type: 'string' },
        { name: 'allowedCardNetworksVisaEnabled', type: 'bool' },
        { name: 'allowedCardNetworksMastercardEnabled', type: 'bool' },
        { name: 'buttonColor', type: 'string' }
    ]
});
// {/block}