// {namespace name="backend/cko_checkout_payment/view/detail/checkout/detail"}
// {block name="backend/cko_checkout_payment/view/detail/checkout/detail"}
Ext.define('Shopware.apps.CkoCheckoutPayment.view.detail.checkout.Detail', {
    extend: 'Ext.form.FieldSet',
    alias: 'widget.order-detail-checkout-detail',
    title: '{s name="backend/cko_checkout_payment/view/detail/checkout/detail/title"}Payment Details{/s}',
    layout: 'hbox',

    initComponent: function () {
        this.items = this.createItems();

        this.callParent(arguments);
    },

    createItems: function () {
        return [
            this.getLeftSideDetailElements(),
            this.getRightSideDetailElements()
        ];
    },

    getLeftSideDetailElements: function () {
        var items = [
            {
                name: 'transactionId',
                fieldLabel: '{s name="field/transactionId/label"}{/s}'
            },
            {
                name: 'paymentId',
                fieldLabel: '{s name="field/paymentId/label"}{/s}'
            },
            {
                name: 'totalAmount',
                fieldLabel: '{s name="field/totalAmount/label"}{/s}'
            },
            {
                name: 'currency',
                fieldLabel: '{s name="field/currency/label"}{/s}'
            },
            {
                name: 'status',
                fieldLabel: '{s name="field/status/label"}{/s}'
            }
        ];

        if (this.isSepaPayment) {
            items.push({
                name: 'sepaMandateReference',
                fieldLabel: '{s name="field/sepaMandateReference/label"}{/s}'
            });
        }

        return {
            xtype: 'container',
            flex: 2,
            defaults: {
                xtype: 'displayfield'
            },
            items: items
        };
    },

    getRightSideDetailElements: function () {
        return {
            xtype: 'container',
            flex: 3,
            items: [
                {
                    xtype: 'base-element-button',
                    disabled: false,
                    hidden: false,
                    text: '{s name="button/void/text"}{/s}',
                    cls: 'primary',
                    itemId: 'buttonVoid',
                    handler: Ext.bind(this.onClickVoidButtonFireEvent, this)
                },
                {
                    xtype: 'base-element-button',
                    disabled: false,
                    hidden: false,
                    text: '{s name="button/refund/text"}{/s}',
                    cls: 'primary',
                    itemId: 'buttonRefund',
                    handler: Ext.bind(this.onClickRefundButtonFireEvent, this)
                },
                {
                    xtype: 'base-element-button',
                    disabled: false,
                    hidden: false,
                    text: '{s name="button/capture/text"}{/s}',
                    cls: 'primary',
                    itemId: 'buttonCapture',
                    handler: Ext.bind(this.onClickCaptureButtonFireEvent, this)
                }
            ]
        };
    },

    onClickVoidButtonFireEvent: function () {
        this.fireEvent('voidPayment');
    },

    onClickRefundButtonFireEvent: function () {
        this.fireEvent('refundPayment');
    },

    onClickCaptureButtonFireEvent: function () {
        this.fireEvent('capturePayment');
    }

});
// {/block}