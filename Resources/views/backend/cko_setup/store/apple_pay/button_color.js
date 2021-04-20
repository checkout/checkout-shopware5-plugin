// {namespace name="backend/cko_setup/store/apple_pay/button_color"}
Ext.define('Shopware.apps.CkoSetup.store.ApplePay.ButtonColor', {
    extend: 'Ext.data.Store',

    storeId: 'CkoSetupApplePayButtonColor',

    fields: [
        { name: 'type', type: 'string' },
        { name: 'text', type: 'string' }
    ],

    data: [
        { type: 'white', text: '{s name="white"}{/s}' },
        { type: 'black', text: '{s name="black"}{/s}' }
    ]
});