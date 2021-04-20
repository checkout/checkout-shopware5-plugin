// {block name="backend/cko_setup/model/sepa/configuration"}
Ext.define('Shopware.apps.CkoSetup.model.Sepa.Configuration', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'CkoSetupSepa'
        };
    },

    fields: [
        // {block name="backend/cko_setup/model/sepa/configuration/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'shopId', type: 'int' },
        { name: 'mandateCreditorName', type: 'string' },
        { name: 'mandateCreditorId', type: 'string' },
        { name: 'mandateCreditorAddressFirst', type: 'string' },
        { name: 'mandateCreditorAddressSecond', type: 'string' },
        { name: 'mandateCreditorCountry', type: 'string' }
    ]
});
// {/block}