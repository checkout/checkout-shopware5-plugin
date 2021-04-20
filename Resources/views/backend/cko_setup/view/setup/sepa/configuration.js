// {namespace name="backend/cko_setup/view/setup/sepa/configuration"}
// {block name="backend/cko_setup/view/setup/sepa/configuration"}
Ext.define('Shopware.apps.CkoSetup.view.setup.Sepa.Configuration', {
    extend: 'Ext.form.Panel',
    alias: 'widget.cko-setup-sepa-configuration',
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
                    xtype: 'textfield',
                    name: 'mandateCreditorName',
                    fieldLabel: '{s name="fieldset/configuration/mandateCreditorName"}{/s}',
                    helpText: '{s name="fieldset/configuration/mandateCreditorName/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'mandateCreditorId',
                    fieldLabel: '{s name="fieldset/configuration/mandateCreditorId"}{/s}',
                    helpText: '{s name="fieldset/configuration/mandateCreditorId/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'mandateCreditorAddressFirst',
                    fieldLabel: '{s name="fieldset/configuration/mandateCreditorAddressFirst"}{/s}',
                    helpText: '{s name="fieldset/configuration/mandateCreditorAddressFirst/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'mandateCreditorAddressSecond',
                    fieldLabel: '{s name="fieldset/configuration/mandateCreditorAddressSecond"}{/s}',
                    helpText: '{s name="fieldset/configuration/mandateCreditorAddressSecond/help"}{/s}',
                    allowBlank: false
                },
                {
                    xtype: 'textfield',
                    name: 'mandateCreditorCountry',
                    fieldLabel: '{s name="fieldset/configuration/mandateCreditorCountry"}{/s}',
                    helpText: '{s name="fieldset/configuration/mandateCreditorCountry/help"}{/s}',
                    allowBlank: false
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