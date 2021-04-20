// {namespace name="backend/cko_setup/view/setup/paypal/window"}
// {block name="backend/cko_setup/view/setup/paypal/window"}
Ext.define('Shopware.apps.CkoSetup.view.setup.Paypal.Window', {
    extend: 'Ext.container.Container',

    alias: 'widget.cko-setup-paypal-window',
    height: 450,

    title: '{s name=window/tab/title}{/s}',
    layout: 'fit',
    style: {
        background: '#F0F2F4;'
    },

    initComponent: function () {
        var me = this;

        me.items = [
            me.createTabPanel()
        ];

        me.callParent(arguments);

        me.on('activate', function() {
            me.tabPanel.getActiveTab().fireEvent('activate', me.tabPanel.getActiveTab());
        });
    },

    createTabPanel: function () {
        var me = this,
            items = [];

        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.Paypal.Configuration', {
            itemId: 'paypalConfiguration',
            border: false
        }));

        me.tabPanel = Ext.create('Ext.tab.Panel', {
            name: 'paypal-main-tab',
            items: items
        });

        return me.tabPanel;
    }
});
// {/block}
