// {namespace name=backend/cko_setup/view/window}
// {block name="backend/cko_setup/view/window"}
Ext.define('Shopware.apps.CkoSetup.view.Window', {
    extend: 'Enlight.app.Window',

    alias: 'widget.cko-setup-window',

    height: 900,
    width: 1600,

    layout: 'fit',

    title: '{s name="window/tab/title"}{/s}',

    topToolbar: null,

    initComponent: function() {
        var me = this;

        me.items = [me.createTabPanel()];
        me.dockedItems = [me.createTopToolbar()];

        me.callParent(arguments);
    },

    createTabPanel: function() {
        var items = [];

        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.GeneralConfiguration.Window'));
        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.ApplePay.Window'));
        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.GooglePay.Window'));
        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.Sepa.Window'));
        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.CreditCard.Window'));
        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.Paypal.Window'));
        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.Sofort.Window'));

        return Ext.create('Ext.tab.Panel', {
            name: 'main-tab',
            items: items
        });
    },

    createTopToolbar: function() {
        var me = this;

        me.topToolbar = Ext.create('Shopware.apps.CkoSetup.view.TopToolbar');

        return me.topToolbar;
    }
});
// {/block}
