// {namespace name="backend/cko_checkout_payment/view/detail/checkout/history"}
// {block name="backend/cko_checkout_payment/view/detail/checkout/history"}
Ext.define('Shopware.apps.CkoCheckoutPayment.view.detail.checkout.History', {
    extend: 'Ext.form.Panel',
    alias: 'widget.order-detail-checkout-tab-history',
    title: '{s name="tab/history/title"}{/s}',
    layout: 'fit',
    border: false,

    historyGrid: null,

    initComponent: function () {
        this.items = this.createItems();

        this.callParent(arguments);
    },

    createItems: function () {
        this.historyGrid = Ext.create('Ext.grid.Panel', {
            anchor: '100%',
            border: false,
            autoScroll: true,
            minHeight: 200,
            columns: [
                { text: '{s name="grid/history/column/type"}{/s}', dataIndex: 'type', flex: 1, renderer: this.transactionTypeRenderer },
                { text: '{s name="grid/history/column/amount"}{/s}', dataIndex: 'amount', flex: 1, renderer: this.currencyRenderer },
                { text: '{s name="grid/history/column/approved"}{/s}', dataIndex: 'isApproved', flex: 1, renderer: this.approvedRenderer },
                { text: '{s name="grid/history/column/date"}{/s}', dataIndex: 'date', flex: 2 }
            ]
        });

        return this.historyGrid;
    },

    transactionTypeRenderer: function (value) {
        switch (value) {
            case 'refund':
                return '{s name="type/refund"}{/s}';
            case 'capture':
                return '{s name="type/capture"}{/s}';
            case 'authorization':
                return '{s name="type/authorization"}{/s}';
            case 'void':
                return '{s name="type/void"}{/s}';
        }
    },

    currencyRenderer: function (value) {
        return Ext.util.Format.currency(value);
    },

    approvedRenderer: function (value) {
        if (value) {
            return '{s name="transaction/approved"}{/s}';
        }

        return '{s name="transaction/notApproved"}{/s}';
    },

});
// {/block}