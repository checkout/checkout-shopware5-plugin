// {namespace name=backend/cko_checkout_payment/view/detail/checkout/refund/window}
// {block name="backend/cko_checkout_payment/view/detail/checkout/refund/window"}
Ext.define('Shopware.apps.CkoCheckoutPayment.view.detail.checkout.refund.Window', {
    extend: 'Enlight.app.Window',
    alias: 'widget.checkout-payment-order-detail-refund-window',

    width: 580,
    height: '20%',
    layout: 'fit',

    title: '{s name=title}{/s}',

    refundRecord: null,

    initComponent: function() {
        this.items = this.createItems();
        this.dockedItems = this.createDockedItems();
        this.refundRecord = this.createRefundRecord();

        this.callParent(arguments);
    },

    createItems: function() {
        return [{
            xtype: 'container',
            style: 'background-color: #F0F2F4;',
            padding: 10,
            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            items: [{
                xtype: 'form',
                layout: {
                    type: 'hbox'
                },
                itemId: 'refundPaymentForm',
                padding: 10,
                border: false,
                items: [{
                    xtype: 'container',
                    flex: 1,
                    layout: 'anchor',
                    defaults: {
                        anchor: '100%'
                    },
                    items: [{
                        xtype: 'numberfield',
                        itemId: 'refundAmountField',
                        readOnly: false,
                        fieldLabel: '{s name=field/refundAmount/label}{/s}',
                        helpText: '{s name=field/refundAmount/helpText}{/s}',
                        name: 'refundAmount',
                        allowBlank: true
                    }]
                }]
            }]
        }];
    },

    createDockedItems: function() {
        var me = this;

        return [
            Ext.create('Ext.toolbar.Toolbar', {
                xtype: 'toolbar',
                dock: 'bottom',
                ui: 'shopware-ui',
                cls: 'shopware-toolbar',
                style: {
                    backgroundColor: '#F0F2F4',
                    borderRight: '1px solid #A4B5C0',
                    borderLeft: '1px solid #A4B5C0',
                    borderTop: '1px solid #A4B5C0',
                    borderBottom: '1px solid #A4B5C0'
                },
                items: ['->', {
                    text: '{s name=close}{/s}',
                    cls: 'secondary',
                    handler: function() {
                        me.close();
                    }
                }, {
                    text: '{s name=submitRefund}{/s}',
                    cls: 'primary',
                    itemId: 'savebutton',
                    disabled: false,
                    handler: function() {
                        var refundAmountField = me.down('#refundAmountField'),
                            refundAmount = refundAmountField.getValue();

                        me.refundRecord.set('refundAmount', refundAmount);
                        me.refundRecord.set('isPartialRefund', refundAmount < me.paymentRecord.get('totalAmount'));

                        me.fireEvent('refundPaymentOnWindow', me.refundRecord);
                        //me.close();
                    }
                }]
            })
        ];
    },

    createRefundRecord: function () {
        return Ext.create('Shopware.apps.CkoCheckoutPayment.model.Refund');
    }
});
// {/block}