// {namespace name="backend/cko_setup/view/setup/apple_pay/configuration"}
// {block name="backend/cko_setup/view/setup/apple_pay/configuration"}
Ext.define('Shopware.apps.CkoSetup.view.setup.ApplePay.Configuration', {
    extend: 'Ext.form.Panel',
    alias: 'widget.cko-setup-applepay-configuration',
    title: '{s name="window/tab/title"}{/s}',

    anchor: '100%',
    border: false,
    bodyPadding: 10,

    style: {
        background: '#EBEDEF'
    },

    fieldDefaults: {
        anchor: '100%',
        labelWidth: 180
    },

    generalConfigurationContainer: null,
    certificateConfigurationContainer: null,

    initComponent: function () {
        var me = this;

        me.items = me.createItems();

        me.callParent(arguments);

        me.toolbarContainer.setBodyStyle(me.style);
    },

    registerEvents: function () {
        var me = this;

        me.addEvents(
            'saveConfiguration'
        );
    },

    createItems: function () {
        var me = this;

        return [
            me.createGeneralConfigurationContainer(),
            me.createCertificateConfigurationContainer()
        ];
    },

    createGeneralConfigurationContainer: function() {
        var me = this;

        me.generalConfigurationContainer = Ext.create('Ext.form.FieldSet', {
            title: '{s name="fieldset/generalConfiguration/title"}{/s}',

            items: [
                {
                    xtype: 'checkbox',
                    name: 'autoCaptureEnabled',
                    inputValue: true,
                    uncheckedValue: false,
                    fieldLabel: '{s name="fieldset/configuration/autoCaptureEnabled"}{/s}',
                    boxLabel: '{s name="fieldset/configuration/autoCaptureEnabled/help"}{/s}'
                },
                {
                    xtype: 'textfield',
                    name: 'merchantId',
                    fieldLabel: '{s name="fieldset/configuration/merchantId"}{/s}',
                    helpText: '{s name="fieldset/configuration/merchantId/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'checkbox',
                    name: 'supportedNetworksAmexEnabled',
                    inputValue: true,
                    uncheckedValue: false,
                    fieldLabel: '{s name="fieldset/configuration/supportedNetworksAmexEnabled"}{/s}',
                    boxLabel: '{s name="fieldset/configuration/supportedNetworksAmexEnabled/help"}{/s}'
                },
                {
                    xtype: 'checkbox',
                    name: 'supportedNetworksMastercardEnabled',
                    inputValue: true,
                    uncheckedValue: false,
                    fieldLabel: '{s name="fieldset/configuration/supportedNetworksMastercardEnabled"}{/s}',
                    boxLabel: '{s name="fieldset/configuration/supportedNetworksMastercardEnabled/help"}{/s}'
                },
                {
                    xtype: 'checkbox',
                    name: 'supportedNetworksVisaEnabled',
                    inputValue: true,
                    uncheckedValue: false,
                    fieldLabel: '{s name="fieldset/configuration/supportedNetworksVisaEnabled"}{/s}',
                    boxLabel: '{s name="fieldset/configuration/supportedNetworksVisaEnabled/help"}{/s}'
                },
                {
                    xtype: 'checkbox',
                    name: 'merchantCapabilitiesCreditEnabled',
                    inputValue: true,
                    uncheckedValue: false,
                    fieldLabel: '{s name="fieldset/configuration/merchantCapabilitiesCreditEnabled"}{/s}',
                    boxLabel: '{s name="fieldset/configuration/merchantCapabilitiesCreditEnabled/help"}{/s}'
                },
                {
                    xtype: 'checkbox',
                    name: 'merchantCapabilitiesDebitEnabled',
                    inputValue: true,
                    uncheckedValue: false,
                    fieldLabel: '{s name="fieldset/configuration/merchantCapabilitiesDebitEnabled"}{/s}',
                    boxLabel: '{s name="fieldset/configuration/merchantCapabilitiesDebitEnabled/help"}{/s}'
                },
                {
                    xtype: 'checkbox',
                    name: 'merchantCapabilities3dsEnabled',
                    inputValue: true,
                    uncheckedValue: false,
                    fieldLabel: '{s name="fieldset/configuration/merchantCapabilities3dsEnabled"}{/s}',
                    boxLabel: '{s name="fieldset/configuration/merchantCapabilities3dsEnabled/help"}{/s}'
                },
                {
                    xtype: 'combobox',
                    name: 'buttonColor',
                    fieldLabel: '{s name="fieldset/configuration/buttonColor"}{/s}',
                    helpText: '{s name="fieldset/configuration/buttonColor/help"}{/s}',
                    store: Ext.create('Shopware.apps.CkoSetup.store.ApplePay.ButtonColor'),
                    displayField: 'text',
                    valueField: 'type',
                    allowBlank: false,
                    required: true
                }
            ]
        });

        return me.generalConfigurationContainer;
    },

    createCertificateConfigurationContainer: function () {
        var me = this;

        me.toolbarContainer = me.createToolbar();

        me.certificateConfigurationContainer = Ext.create('Ext.form.FieldSet', {
            title: '{s name="fieldset/certificateConfiguration/title"}{/s}',

            items: [
                {
                    xtype: 'textfield',
                    name: 'csrCommonName',
                    fieldLabel: '{s name="fieldset/configuration/csrCommonName"}{/s}',
                    helpText: '{s name="fieldset/configuration/csrCommonName/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'csrOrganizationName',
                    fieldLabel: '{s name="fieldset/configuration/csrOrganizationName"}{/s}',
                    helpText: '{s name="fieldset/configuration/csrOrganizationName/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'csrOrganizationUnitName',
                    fieldLabel: '{s name="fieldset/configuration/csrOrganizationUnitName"}{/s}',
                    helpText: '{s name="fieldset/configuration/csrOrganizationUnitName/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'csrLocalityName',
                    fieldLabel: '{s name="fieldset/configuration/csrLocalityName"}{/s}',
                    helpText: '{s name="fieldset/configuration/csrLocalityName/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'csrStateOrProvinceName',
                    fieldLabel: '{s name="fieldset/configuration/csrStateOrProvinceName"}{/s}',
                    helpText: '{s name="fieldset/configuration/csrStateOrProvinceName/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'csrCountryName',
                    fieldLabel: '{s name="fieldset/configuration/csrCountryName"}{/s}',
                    helpText: '{s name="fieldset/configuration/csrCountryName/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'csrEmailAddress',
                    fieldLabel: '{s name="fieldset/configuration/csrEmailAddress"}{/s}',
                    helpText: '{s name="fieldset/configuration/csrEmailAddress/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    inputType: 'password',
                    name: 'csrCertificatePassword',
                    fieldLabel: '{s name="fieldset/configuration/csrCertificatePassword"}{/s}',
                    helpText: '{s name="fieldset/configuration/csrCertificatePassword/help"}{/s}',
                    allowBlank: true
                },

                me.toolbarContainer
            ]
        });

        return me.certificateConfigurationContainer;
    },

    createToolbar: function () {
        var me = this;

        return Ext.create('Ext.form.Panel', {
            dock: 'bottom',
            border: false,
            bodyPadding: 5,
            name: 'toolbarContainer',

            items: [{
                xtype: 'button',
                cls: 'primary',
                padding: '10px 200px',
                text: '{s name="fieldset/configuration/saveButton"}{/s}',
                style: {
                    float: 'right'
                },
                handler: function () {
                    me.fireEvent('saveConfiguration')
                }
            }]
        });
    }
});
// {/block}