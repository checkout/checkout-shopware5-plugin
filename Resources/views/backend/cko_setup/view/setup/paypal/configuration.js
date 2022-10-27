// {namespace name="backend/cko_setup/view/setup/paypal/configuration"}
// {block name="backend/cko_setup/view/setup/paypal/configuration"}
Ext.define('Shopware.apps.CkoSetup.view.setup.Paypal.Configuration', {
    extend: 'Ext.form.Panel',
    alias: 'widget.cko-setup-paypal-configuration',
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