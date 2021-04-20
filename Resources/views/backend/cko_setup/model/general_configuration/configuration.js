// {block name="backend/cko_setup/model/general_configuration/configuration"}
Ext.define('Shopware.apps.CkoSetup.model.GeneralConfiguration.Configuration', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'CkoSetupGeneralConfiguration'
        };
    },

    fields: [
        // {block name="backend/cko_setup/model/general_configuration/configuration/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'shopId', type: 'int' },
        { name: 'sandboxModeEnabled', type: 'bool' },
        { name: 'privateKey', type: 'string' },
        { name: 'publicKey', type: 'string' }
    ]
});
// {/block}