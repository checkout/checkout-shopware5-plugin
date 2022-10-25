// {namespace name="backend/cko_setup/view/setup/general_configuration/configuration"}
// {block name="backend/cko_setup/view/setup/general_configuration/configuration"}
Ext.define('Shopware.apps.CkoSetup.view.setup.GeneralConfiguration.Configuration', {
    extend: 'Ext.form.Panel',
    alias: 'widget.cko-setup-general-configuration',
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
            'saveConfiguration',
            'checkApiCredentials',
            'registerWebhooks'
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
                    name: 'sandboxModeEnabled',
                    inputValue: true,
                    uncheckedValue: false,
                    fieldLabel: '{s name="fieldset/configuration/sandboxModeEnabled"}{/s}',
                    boxLabel: '{s name="fieldset/configuration/sandboxModeEnabled/help"}{/s}'
                },
                {
                    xtype: 'textfield',
                    name: 'privateKey',
                    fieldLabel: '{s name="fieldset/configuration/privateKey"}{/s}',
                    helpText: '{s name="fieldset/configuration/privateKey/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'publicKey',
                    fieldLabel: '{s name="fieldset/configuration/publicKey"}{/s}',
                    helpText: '{s name="fieldset/configuration/publicKey/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'webhookSignatureKey',
                    fieldLabel: '{s name="fieldset/configuration/webhookSignatureKey"}{/s}',
                    helpText: '{s name="fieldset/configuration/webhookSignatureKey/help"}{/s}',
                    allowBlank: true
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
                    float: 'left'
                },
                handler: function () {
                    me.fireEvent('saveConfiguration')
                }
            }, {
                xtype: 'button',
                cls: 'primary',
                iconCls: 'sprite-gear',
                padding: '12px 20px',
                text: '{s name="fieldset/configuration/checkApiCredentialsButton"}{/s}',
                style: {
                    float: 'right'
                },
                handler: function () {
                    me.fireEvent('checkApiCredentials');
                }
            }, {
                xtype: 'button',
                cls: 'primary',
                iconCls: 'sprite-lightning',
                padding: '12px 20px',
                text: '{s name="fieldset/configuration/registerWebhooksButton"}{/s}',
                style: {
                    float: 'right'
                },
                handler: function () {
                    me.fireEvent('registerWebhooks');
                }
            }]
        });
    }
});
// {/block}