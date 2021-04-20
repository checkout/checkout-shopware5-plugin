Ext.define('Shopware.apps.CkoSetup.store.ApplePay', {
    extend: 'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'CkoSetupApplePay'
        };
    },
    model: 'Shopware.apps.CkoSetup.model.ApplePay'
});
