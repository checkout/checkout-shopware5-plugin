;(function ($, window) {
    'use strict';

    $.plugin('ckoCheckoutPaymentIdeal', {
        defaults: {
            shippingPaymentFormSelector: '#shippingPaymentForm',
            idealFormSelector: '[data-cko-checkout-payment-ideal="true"]',
            idealBicSelector: '#cko-ideal-bic-field',
            idealSavePaymentDataIdentifier: 'cko-checkout-payment-ideal-request-url'
        },

        $shippingPaymentForm: null,
        $idealForm: null,
        $idealBic: null,

        shippingPaymentRequestUrl: null,
        idealSavePaymentDataRequestUrl: null,

        init: function () {
            this.$shippingPaymentForm = $(this.opts.shippingPaymentFormSelector);
            this.$idealForm = this.$shippingPaymentForm.find(this.opts.idealFormSelector);
            this.$idealBic = this.$idealForm.find(this.opts.idealBicSelector);

            this.shippingPaymentRequestUrl = this.$shippingPaymentForm.attr('action') || null;
            this.idealSavePaymentDataRequestUrl = this.$idealForm.data(this.opts.idealSavePaymentDataIdentifier) || null;

            this.registerEventListeners();

            $.publish('plugin/ckoCheckoutPaymentIdeal/init', this);
        },

        registerEventListeners: function () {
            this.$idealForm.on('change', $.proxy(this.onChangeIdealForm, this));

            $.subscribe('plugin/swShippingPayment/onInputChanged', $.proxy(this.onPaymentMethodChange, this));
            $.publish('plugin/ckoCheckoutPaymentIdeal/registerEventListeners', this);
        },

        onChangeIdealForm: function (event) {
            $.loadingIndicator.open({
                openOverlay: true,
                closeOnClick: false
            });

            $.ajax({
                url: this.idealSavePaymentDataRequestUrl,
                method: 'POST',
                cache: false,
                data: {
                    ckoBic: this.$idealBic.val()
                }
            }).done(function () {
                $.publish('plugin/ckoCheckoutPaymentIdeal/onChangeIdealForm/done', this, event);

                $.loadingIndicator.close();
            }).fail(function () {
                $.publish('plugin/ckoCheckoutPaymentIdeal/onChangeIdealForm/failed', this, event);

                $.loadingIndicator.close();
            });
        },

        onPaymentMethodChange: function () {
            // we need to reinitialize payment handler on payment method change
            window.StateManager.addPlugin('*form[id="shippingPaymentForm"]', 'ckoCheckoutPaymentIdeal');

            $.publish('plugin/ckoCheckoutPaymentIdeal/onPaymentMethodInputChange', this);
        }
    });

    window.StateManager.addPlugin('*form[id="shippingPaymentForm"]', 'ckoCheckoutPaymentIdeal');
})(jQuery, window);
