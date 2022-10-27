// {namespace name=backend/cko_checkout_payment/view/detail/checkout/capture/window}
// {block name="backend/cko_checkout_payment/view/detail/checkout/capture/window"}
Ext.define('Shopware.apps.CkoCheckoutPayment.view.detail.checkout.capture.Window', {
    extend: 'Enlight.app.Window',
    alias: 'widget.checkout-payment-order-detail-capture-window',

    width: 580,
    height: '20%',
    layout: 'fit',

    title: '{s name="title"}{/s}',

    captureRecord: null,

    initComponent: function() {
        this.items = this.createItems();
        this.dockedItems = this.createDockedItems();
        this.captureRecord = this.createCaptureRecord();

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
                itemId: 'capturePaymentForm',
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
                        itemId: 'captureAmountField',
                        readOnly: false,
                        fieldLabel: '{s name="field/captureAmount/label"}{/s}',
                        helpText: '{s name="field/captureAmount/helpText"}{/s}',
                        name: 'captureAmount',
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
                    text: '{s name="close"}{/s}',
                    cls: 'secondary',
                    handler: function() {
                        me.close();
                    }
                }, {
                    text: '{s name="submitCapture"}{/s}',
                    cls: 'primary',
                    itemId: 'savebutton',
                    disabled: false,
                    handler: function() {
                        var captureAmountField = me.down('#captureAmountField'),
                            captureAmount = captureAmountField.getValue();

                        me.captureRecord.set('captureAmount', captureAmount);
                        me.captureRecord.set('isPartialCapture', captureAmount < me.paymentRecord.get('totalAmount'));

                        me.fireEvent('capturePaymentOnWindow', me.captureRecord);
                        //me.close();
                    }
                }]
            })
        ];
    },

    createCaptureRecord: function () {
        return Ext.create('Shopware.apps.CkoCheckoutPayment.model.Capture');
    }
});
// {/block}