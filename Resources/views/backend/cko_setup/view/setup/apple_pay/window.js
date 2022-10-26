// {namespace name="backend/cko_setup/view/setup/apple_pay/window"}
// {block name="backend/cko_setup/view/setup/apple_pay/window"}
Ext.define('Shopware.apps.CkoSetup.view.setup.ApplePay.Window', {
    extend: 'Ext.container.Container',

    alias: 'widget.cko-setup-applepay-window',
    height: 450,

    title: '{s name="window/tab/title"}{/s}',
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

        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.ApplePay.Configuration', {
            itemId: 'applePayConfiguration',
            border: false
        }));

        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.ApplePay.DomainVerify', {
            itemId: 'applePayDomainVerify',
            border: false
        }));

        items.push(Ext.create('Shopware.apps.CkoSetup.view.setup.ApplePay.MerchantIdentityCertificate', {
            itemId: 'applePayMerchantIdentityCertificate',
            border: false
        }));

        me.tabPanel = Ext.create('Ext.tab.Panel', {
            name: 'apple-pay-main-tab',
            items: items
        });

        return me.tabPanel;
    }
});
// {/block}
