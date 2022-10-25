//{block name="backend/cko_setup/application"}
Ext.define('Shopware.apps.CkoSetup', {
    extend: 'Enlight.app.SubApplication',

    name: 'Shopware.apps.CkoSetup',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [
        'Main',
        'GeneralConfiguration',
        'ApplePay',
        'GooglePay',
        'Sepa',
        'Sofort',
        'CreditCard',
        'Paypal'
    ],

    views: [
        'Window',
        'setup.GeneralConfiguration.Window',
        'setup.GeneralConfiguration.Configuration',
        'setup.ApplePay.Window',
        'setup.ApplePay.MerchantIdentityCertificate',
        'setup.ApplePay.DomainVerify',
        'setup.ApplePay.Configuration',
        'setup.GooglePay.Window',
        'setup.GooglePay.Configuration',
        'setup.Sepa.Window',
        'setup.Sepa.Configuration',
        'setup.Sofort.Window',
        'setup.Sofort.Configuration',
        'setup.CreditCard.Window',
        'setup.CreditCard.Configuration',
        'setup.Paypal.Window',
        'setup.Paypal.Configuration',
        'TopToolbar'
    ],

    models: [
        'GeneralConfiguration.Configuration',
        'ApplePay.Configuration',
        'GooglePay.Configuration',
        'Sepa.Configuration',
        'Sofort.Configuration',
        'CreditCard.Configuration',
        'Paypal.Configuration'
    ],

    stores: [
        'ApplePay.SupportedNetworks',
        'ApplePay.MerchantCapabilities',
        'GooglePay.AllowedCardNetworks',
        'GooglePay.ButtonColor'
    ],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});
//{/block}

