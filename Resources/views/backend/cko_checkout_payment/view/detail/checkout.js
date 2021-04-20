// {namespace name="backend/cko_checkout_payment/view/detail/checkout"}
// {block name="backend/cko_checkout_payment/view/detail/checkout"}
Ext.define('Shopware.apps.CkoCheckoutPayment.view.detail.Checkout', {
    extend: 'Ext.form.Panel',
    alias: 'widget.order-detail-checkout-tab',
    id: 'checkoutDetailTab',
    title: '{s name="tab/title"}{/s}',

    autoScroll: true,
    bodyPadding: 10,

    historyTab: null,

    initComponent: function () {
        this.items = [
            this.createDetailContainer(),
            this.createTabPanels()
        ];

        this.callParent(arguments);
    },

    createDetailContainer: function () {
        return Ext.create('Shopware.apps.CkoCheckoutPayment.view.detail.checkout.Detail', {
            isSepaPayment: this.orderRecord.raw.payment.name === 'cko_sepa'
        });
    },

    createTabPanels: function () {
        this.historyTab = Ext.create('Shopware.apps.CkoCheckoutPayment.view.detail.checkout.History');

        return Ext.create('Ext.tab.Panel', {
            anchor: '100%',
            border: false,
            items: [
                this.historyTab
            ]
        });
    },

    reloadFields: function () {
        var record = this.getRecord(),
            buttonCapture = this.down('#buttonCapture'),
            buttonVoid = this.down('#buttonVoid'),
            buttonRefund = this.down('#buttonRefund');

        if (record.get('isCapturePossible')) {
            buttonCapture.enable();
            buttonCapture.setDisabled(false);
        } else {
            buttonCapture.disable();
            buttonCapture.setDisabled(true);
        }

        if (record.get('isVoidPossible')) {
            buttonVoid.enable();
            buttonVoid.setDisabled(false);
        } else {
            buttonVoid.disable();
            buttonVoid.setDisabled(true);
        }

        if (record.get('isRefundPossible')) {
            buttonRefund.enable();
            buttonRefund.setDisabled(false);
        } else {
            buttonRefund.disable();
            buttonRefund.setDisabled(true);
        }

        this.historyTab.historyGrid.reconfigure(record.transactions());

        return true;
    }

});
// {/block}