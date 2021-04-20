// {block name="backend/cko_setup/model/credit_card/configuration"}
Ext.define('Shopware.apps.CkoSetup.model.CreditCard.Configuration', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'CkoSetupCreditCard'
        };
    },

    fields: [
        // {block name="backend/cko_setup/model/credit_card/configuration/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'shopId', type: 'int' },
        { name: 'autoCaptureEnabled', type: 'bool' },
        { name: 'threeDsEnabled', type: 'bool' },
        { name: 'n3dAttemptEnabled', type: 'bool' },
        { name: 'dynamicBillingDescriptorEnabled', type: 'bool' },
        { name: 'dynamicBillingDescriptorName', type: 'string' },
        { name: 'dynamicBillingDescriptorCity', type: 'string' },
        { name: 'saveCardOptionEnabled', type: 'bool' }
    ]
});
// {/block}