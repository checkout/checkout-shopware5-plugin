// {block name="backend/cko_setup/model/sofort/configuration"}
Ext.define('Shopware.apps.CkoSetup.model.Sofort.Configuration', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'CkoSetupSofort'
        };
    },

    fields: [
        // {block name="backend/cko_setup/model/sofort/configuration/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'shopId', type: 'int' },
        { name: 'paymentStatusAuthId', type: 'int' },
    ]
});
// {/block}