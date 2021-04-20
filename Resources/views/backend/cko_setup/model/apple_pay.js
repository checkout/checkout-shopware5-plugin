Ext.define('Shopware.apps.CkoSetup.model.ApplePay', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'CkoSetupApplePay'
        };
    },

    fields: [
        { name : 'id', type: 'int', useNull: true },
    ]
});
