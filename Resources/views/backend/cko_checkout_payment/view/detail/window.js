// {block name="backend/order/view/detail/window"}
// {$smarty.block.parent}
Ext.define('Shopware.apps.CkoCheckoutPayment.view.detail.Window', {
    alias: 'widget.order-detail-checkout-payment',
    override: 'Shopware.apps.Order.view.detail.Window',

    checkoutPaymentTab: null,

    initComponent: function () {
        this.callParent(arguments);
    },

    createTabPanel: function () {
        var me = this,
            tabPanel = this.callParent(arguments),
            payment = this.record.getPayment().first(),
            isCheckoutPayment = payment.get('name').substr(0, 4) === 'cko_';

        if (!isCheckoutPayment) {
            return tabPanel;
        }

        this.checkoutPaymentTab = this.createCheckoutPaymentTab();
        tabPanel.add(this.checkoutPaymentTab);

        tabPanel.on('tabchange', function (tabPanel, newCard) {
            if(newCard.getId() === 'checkoutDetailTab'){
                me.fireEvent('checkoutPaymentOrderTabOpen', me, me.record);
            }
            return true;
        });

        return tabPanel;
    },

    createCheckoutPaymentTab: function () {
        return Ext.create('Shopware.apps.CkoCheckoutPayment.view.detail.Checkout', {
            orderRecord: this.record
        });
    }
});
// {/block}