// {namespace name="backend/cko_setup/store/apple_pay/supported_networks"}
Ext.define('Shopware.apps.CkoSetup.store.ApplePay.SupportedNetworks', {
    extend: 'Ext.data.Store',

    storeId: 'CkoSetupApplePaySupportedNetworks',

    fields: [
        { name: 'type', type: 'string' },
        { name: 'text', type: 'string' }
    ],

    data: [
        { type: 'amex', text: '{s name="amex"}{/s}' },
        { type: 'masterCard', text: '{s name="mastercard"}{/s}' },
        { type: 'visa', text: '{s name="visa"}{/s}' }
    ]
});