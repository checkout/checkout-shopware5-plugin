// {namespace name="backend/cko_setup/view/setup/google_pay/configuration"}
// {block name="backend/cko_setup/view/setup/google_pay/configuration"}
Ext.define('Shopware.apps.CkoSetup.view.setup.GooglePay.Configuration', {
    extend: 'Ext.form.Panel',
    alias: 'widget.cko-setup-google-pay-configuration',
    title: '{s name=window/tab/title}{/s}',

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

    configurationContainer: null,

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
            me.createConfigurationContainer()
        ];
    },

    createConfigurationContainer: function () {
        var me = this;

        me.toolbarContainer = me.createToolbar();

        me.configurationContainer = Ext.create('Ext.form.FieldSet', {
            title: '{s name="fieldset/configuration/title"}{/s}',

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
                    name: 'allowedCardNetworksVisaEnabled',
                    inputValue: true,
                    uncheckedValue: false,
                    fieldLabel: '{s name="fieldset/configuration/allowedCardNetworksVisaEnabled"}{/s}',
                    boxLabel: '{s name="fieldset/configuration/allowedCardNetworksVisaEnabled/help"}{/s}'
                },
                {
                    xtype: 'checkbox',
                    name: 'allowedCardNetworksMastercardEnabled',
                    inputValue: true,
                    uncheckedValue: false,
                    fieldLabel: '{s name="fieldset/configuration/allowedCardNetworksMastercardEnabled"}{/s}',
                    boxLabel: '{s name="fieldset/configuration/allowedCardNetworksMastercardEnabled/help"}{/s}'
                },
                {
                    xtype: 'combobox',
                    name: 'buttonColor',
                    fieldLabel: '{s name="fieldset/configuration/buttonColor"}{/s}',
                    helpText: '{s name="fieldset/configuration/buttonColor/help"}{/s}',
                    store: Ext.create('Shopware.apps.CkoSetup.store.GooglePay.ButtonColor'),
                    displayField: 'text',
                    valueField: 'type',
                    allowBlank: false,
                    required: true
                },

                me.toolbarContainer
            ]
        });

        return me.configurationContainer;
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