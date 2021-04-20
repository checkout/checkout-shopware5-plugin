// {namespace name="backend/cko_setup/store/google_pay/allowed_card_networks"}
Ext.define('Shopware.apps.CkoSetup.store.GooglePay.AllowedCardNetworks', {
    extend: 'Ext.data.Store',

    storeId: 'CkoSetupApplePayAllowedCardNetworks',

    fields: [
        { name: 'type', type: 'string' },
        { name: 'text', type: 'string' }
    ],

    data: [
        { type: 'VISA', text: '{s name="visa"}{/s}' },
        { type: 'MASTERCARD', text: '{s name="mastercard"}{/s}' }
    ]
});