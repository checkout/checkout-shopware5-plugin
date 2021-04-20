// {namespace name="backend/cko_setup/store/google_pay/button_color"}
Ext.define('Shopware.apps.CkoSetup.store.GooglePay.ButtonColor', {
    extend: 'Ext.data.Store',

    storeId: 'CkoSetupGooglePayButtonColor',

    fields: [
        { name: 'type', type: 'string' },
        { name: 'text', type: 'string' }
    ],

    data: [
        { type: 'white', text: '{s name="white"}{/s}' },
        { type: 'black', text: '{s name="black"}{/s}' }
    ]
});