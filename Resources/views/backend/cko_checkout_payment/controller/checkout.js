// {namespace name="backend/cko_checkout_payment/controller/checkout"}
// {block name="backend/cko_checkout_payment/controller/checkout"}
Ext.define('Shopware.apps.CkoCheckoutPayment.controller.Checkout', {
    extend: 'Enlight.app.Controller',
    override: 'Shopware.apps.Order.controller.Main',
    refs: [
        { ref: 'checkoutTab', selector: 'order-detail-checkout-tab' },
        { ref: 'detailView', selector: 'order-detail-checkout-detail' }
    ],

    getPaymentDetailsUrl: '{url controller=CkoCheckoutPayment action=getPaymentDetails module=backend}',
    capturePaymentUrl: '{url controller=CkoCheckoutPayment action=capturePayment module=backend}',
    refundPaymentUrl: '{url controller=CkoCheckoutPayment action=refundPayment module=backend}',
    voidPaymentUrl: '{url controller=CkoCheckoutPayment action=voidPayment module=backend}',

    paymentStore: null,

    orderRecord: null,
    paymentRecord: null,

    refundWindow: null,
    captureWindow: null,

    init: function () {
        this.registerCustomEventListeners();

        this.callParent(arguments);
    },

    registerCustomEventListeners: function () {
        this.control({
            'order-detail-checkout-payment': {
                'checkoutPaymentOrderTabOpen': this.onOpenCheckoutPaymentTab
            },
            'order-detail-window': {
                'checkoutPaymentOrderTabOpen': this.onOpenCheckoutPaymentTab
            },
            'order-detail-checkout-detail': {
                'voidPayment': Ext.bind(this.onClickVoidPayment, this),
                'refundPayment': Ext.bind(this.onClickRefundPaymentShowWindow, this),
                'capturePayment': Ext.bind(this.onClickCapturePaymentShowWindow, this),
            },
            'checkout-payment-order-detail-capture-window{ isVisible(true) }': {
                capturePaymentOnWindow: Ext.bind(this.onCapturePaymentWindowCapturePayment, this)
            },
            'checkout-payment-order-detail-refund-window{ isVisible(true) }': {
                refundPaymentOnWindow: Ext.bind(this.onRefundPaymentWindowRefundPayment, this)
            },
        });
    },

    showOrder: function (record) {
        this.callParent(arguments);

        this.orderRecord = record;
    },

    onOpenCheckoutPaymentTab: function (window, record) {
        this.orderRecord = record;
        this.getPaymentDetails();
    },

    getPaymentDetails: function () {
        var me = this;

        this.showLoadingIndicator(true);

        Ext.Ajax.request({
            url: this.getPaymentDetailsUrl,
            params: {
                orderId: this.orderRecord.get('id'),
                paymentId: this.orderRecord.get('transactionId'),
                shopId: this.orderRecord.getShop().first().get('id')
            },
            success: function (response) {
                me.showLoadingIndicator(false);

                var decodedResponse = Ext.JSON.decode(response.responseText);

                if (!decodedResponse.success) {
                    me.onLoadPaymentDetailsFailed();

                    return;
                }

                me.setPaymentDetails(decodedResponse.data);
            },
            failure: function () {
                me.onLoadPaymentDetailsFailed();
            }
        });
    },

    onLoadPaymentDetailsFailed: function () {
        var checkoutPaymentTab = this.getCheckoutTab();

        this.showNotificationMessage('{s name="payment/details/loading/error/message"}{/s}');
        this.showLoadingIndicator(false);

        checkoutPaymentTab.setDisabled(true);
    },

    setPaymentDetails: function (payment) {
        var checkoutPaymentTab = this.getCheckoutTab();

        this.showLoadingIndicator(false);

        this.paymentStore = this.createPaymentStore();
        this.paymentStore.loadRawData(payment);
        this.paymentRecord = this.paymentStore.first();

        checkoutPaymentTab.loadRecord(this.paymentRecord);
        checkoutPaymentTab.reloadFields();
    },

    createPaymentStore: function () {
        return Ext.create('Ext.data.Store', {
            model: 'Shopware.apps.CkoCheckoutPayment.model.Payment',
            proxy: {
                type: 'memory',
                reader: {
                    type: 'json'
                }
            }
        });
    },

    showNotificationMessage: function (message) {
        Shopware.Notification.createGrowlMessage(
            '{s name="growl/headerTitle"}{/s}',
            message,
            '{s name="growl/callerTitle"}{/s}'
        );
    },

    showLoadingIndicator: function (isLoading) {
        var checkoutPaymentTab = this.getCheckoutTab();

        if (!checkoutPaymentTab) {
            return;
        }

        checkoutPaymentTab.setDisabled(isLoading !== false);
        checkoutPaymentTab.setLoading(isLoading);
    },

    onClickVoidPayment: function () {
        var me = this;

        Ext.MessageBox.confirm('{s name="growl/headerTitle"}{/s}', '{s name="void/confirmation/message"}{/s}', function (confirmationResponse) {
            if (confirmationResponse !== 'yes') {
                return;
            }

            me.onVoidPaymentConfirmationVoidPayment();
        });
    },

    onClickRefundPaymentShowWindow: function () {
        var me = this;

        this.refundWindow = Ext.create('Shopware.apps.CkoCheckoutPayment.view.detail.checkout.refund.Window', {
            paymentRecord: this.paymentRecord
        });

        this.refundWindow.show(null, function () {
            var refundPaymentForm = this.down('#refundPaymentForm');

            refundPaymentForm.loadRecord(me.orderRecord);
            refundPaymentForm.down('#refundAmountField').setValue(me.paymentRecord.get('remainingRefundAmount'));
        });
    },

    onClickCapturePaymentShowWindow: function () {
        var me = this;

        this.captureWindow = Ext.create('Shopware.apps.CkoCheckoutPayment.view.detail.checkout.capture.Window', {
            paymentRecord: this.paymentRecord
        });

        this.captureWindow.show(null, function () {
            var capturePaymentForm = this.down('#capturePaymentForm');

            capturePaymentForm.loadRecord(me.orderRecord);
            capturePaymentForm.down('#captureAmountField').setValue(me.paymentRecord.get('totalAmount'));
        });
    },

    onCapturePaymentWindowCapturePayment: function (record) {
        var me = this;

        Ext.Ajax.request({
            url: this.capturePaymentUrl,
            method: 'POST',
            params: {
                orderId: this.orderRecord.get('id'),
                paymentId: this.orderRecord.get('transactionId'),
                shopId: this.orderRecord.getShop().first().get('id'),
                reference: this.orderRecord.get('temporaryId'),
                captureAmount: record.get('captureAmount'),
                isPartialCapture: record.get('isPartialCapture')
            },
            success: function (response) {
                var decodedResponse = Ext.JSON.decode(response.responseText);
                if (!decodedResponse.success) {
                    me.showNotificationMessage('{s name="capture/error/message"}{/s}' + ' ' + decodedResponse.error);

                    return;
                }

                me.captureWindow.close();

                me.showNotificationMessage('{s name="capture/success/message"}{/s}');
                me.getPaymentDetails();
            },
            failure: function () {
                me.showNotificationMessage('{s name="capture/error/message"}{/s}');
            }
        });
    },

    onRefundPaymentWindowRefundPayment: function (record) {
        var me = this;

        Ext.Ajax.request({
            url: this.refundPaymentUrl,
            method: 'POST',
            params: {
                paymentId: this.orderRecord.get('transactionId'),
                shopId: this.orderRecord.getShop().first().get('id'),
                reference: this.orderRecord.get('temporaryId'),
                refundAmount: record.get('refundAmount'),
                isPartialRefund: record.get('isPartialRefund')
            },
            success: function (response) {
                var decodedResponse = Ext.JSON.decode(response.responseText);
                if (!decodedResponse.success) {
                    me.showNotificationMessage('{s name="refund/error/message"}{/s}' + ' ' + decodedResponse.error);

                    return;
                }

                me.refundWindow.close();

                me.showNotificationMessage('{s name="refund/success/message"}{/s}');
                me.getPaymentDetails();
            },
            failure: function () {
                me.showNotificationMessage('{s name="refund/error/message"}{/s}');
            }
        });
    },

    onVoidPaymentConfirmationVoidPayment: function () {
        var me = this;

        Ext.Ajax.request({
            url: this.voidPaymentUrl,
            method: 'POST',
            params: {
                orderId: this.orderRecord.get('id'),
                paymentId: this.orderRecord.get('transactionId'),
                shopId: this.orderRecord.getShop().first().get('id'),
                reference: this.orderRecord.get('temporaryId')
            },
            success: function (response) {
                var decodedResponse = Ext.JSON.decode(response.responseText);
                if (!decodedResponse.success) {
                    me.showNotificationMessage('{s name="void/error/message"}{/s}' + ' ' + decodedResponse.error);

                    return;
                }

                me.showNotificationMessage('{s name="void/success/message"}{/s}');
                me.getPaymentDetails();
            },
            failure: function () {
                me.showNotificationMessage('{s name="void/error/message"}{/s}');
            }
        });
    },

});
// {/block}