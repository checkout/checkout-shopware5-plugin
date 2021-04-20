// {block name="backend/cko_setup/model/apple_pay/configuration"}
Ext.define('Shopware.apps.CkoSetup.model.ApplePay.Configuration', {
    extend: 'Shopware.data.Model',

    configure: function() {
        return {
            controller: 'CkoSetupApplePay'
        };
    },

    fields: [
        // {block name="backend/cko_setup/model/apple_pay/configuration/fields"}{/block}
        { name: 'id', type: 'int' },
        { name: 'shopId', type: 'int' },
        { name: 'csrCommonName', type: 'string' },
        { name: 'csrOrganizationName', type: 'string' },
        { name: 'csrOrganizationUnitName', type: 'string' },
        { name: 'csrLocalityName', type: 'string' },
        { name: 'csrStateOrProvinceName', type: 'string' },
        { name: 'csrCountryName', type: 'string' },
        { name: 'csrEmailAddress', type: 'string' },
        { name: 'csrCertificatePassword', type: 'string' },
        { name: 'autoCaptureEnabled', type: 'bool' },
        { name: 'merchantId', type: 'string' },
        { name: 'supportedNetworksAmexEnabled', type: 'bool' },
        { name: 'supportedNetworksMastercardEnabled', type: 'bool' },
        { name: 'supportedNetworksVisaEnabled', type: 'bool' },
        { name: 'merchantCapabilitiesCreditEnabled', type: 'bool' },
        { name: 'merchantCapabilitiesDebitEnabled', type: 'bool' },
        { name: 'merchantCapabilities3dsEnabled', type: 'bool' },
        { name: 'buttonColor', type: 'string' }
    ]
});
// {/block}