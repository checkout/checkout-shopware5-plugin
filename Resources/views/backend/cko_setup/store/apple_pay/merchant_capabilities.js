// {namespace name="backend/cko_setup/store/apple_pay/merchant_capabilities"}
Ext.define('Shopware.apps.CkoSetup.store.ApplePay.MerchantCapabilities', {
    extend: 'Ext.data.Store',

    storeId: 'CkoSetupApplePayMerchantCapabilities',

    fields: [
        { name: 'type', type: 'string' },
        { name: 'text', type: 'string' }
    ],

    data: [
        { type: 'supportsCredit', text: '{s name="credit"}{/s}' },
        { type: 'supportsDebit', text: '{s name="debit"}{/s}' },
        { type: 'supports3DS', text: '{s name="3ds"}{/s}' }
    ]
});