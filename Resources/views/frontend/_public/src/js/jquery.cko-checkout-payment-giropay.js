;(function ($, window) {
    'use strict';

    $.plugin('ckoCheckoutPaymentGiropay', {
        defaults: {
            shippingPaymentFormSelector: '#shippingPaymentForm',
            shippingPaymentSubmitButtonSelector: 'input[type=submit]',
            giropayFormSelector: '[data-cko-checkout-payment-giropay="true"]',
            giropayBicSelector: '#cko-giropay-bic-field',
            giropaySavePaymentDataIdentifier: 'cko-checkout-payment-giropay-request-url'
        },

        $shippingPaymentForm: null,
        $shippingPaymentSubmitButton: null,
        $giropayForm: null,
        $giropayBic: null,

        shippingPaymentRequestUrl: null,
        giropaySavePaymentDataRequestUrl: null,

        init: function () {
            this.$shippingPaymentForm = $(this.opts.shippingPaymentFormSelector);
            this.$shippingPaymentSubmitButton = this.$shippingPaymentForm.find(this.opts.shippingPaymentSubmitButtonSelector);
            this.$giropayForm = this.$shippingPaymentForm.find(this.opts.giropayFormSelector);
            this.$giropayBic = this.$giropayForm.find(this.opts.giropayBicSelector);

            this.shippingPaymentRequestUrl = this.$shippingPaymentForm.attr('action') || null;
            this.giropaySavePaymentDataRequestUrl = this.$giropayForm.data(this.opts.giropaySavePaymentDataIdentifier) || null;

            this.registerEventListeners();

            $.publish('plugin/ckoCheckoutPaymentGiropay/init', this);
        },

        registerEventListeners: function () {
            this.$giropayForm.on('change', $.proxy(this.onChangeGiropayForm, this));

            $.subscribe('plugin/swShippingPayment/onInputChanged', $.proxy(this.onPaymentMethodChange, this));
            $.publish('plugin/ckoCheckoutPaymentGiropay/registerEventListeners', this);
        },

        onChangeGiropayForm: function (event) {
            $.loadingIndicator.open({
                openOverlay: true,
                closeOnClick: false
            });

            $.ajax({
                url: this.giropaySavePaymentDataRequestUrl,
                method: 'POST',
                cache: false,
                data: {
                    ckoBic: this.$giropayBic.val()
                }
            }).done(function () {
                $.publish('plugin/ckoCheckoutPaymentGiropay/onChangeGiropayForm/done', this, event);

                $.loadingIndicator.close();
            }).fail(function () {
                $.publish('plugin/ckoCheckoutPaymentGiropay/onChangeGiropayForm/failed', this, event);

                $.loadingIndicator.close();
            });
        },

        onPaymentMethodChange: function () {
            // we need to reinitialize payment handler on payment method change
            window.StateManager.addPlugin('*form[id="shippingPaymentForm"]', 'ckoCheckoutPaymentGiropay');

            $.publish('plugin/ckoCheckoutPaymentGiropay/onPaymentMethodInputChange', this);
        }
    });

    window.StateManager.addPlugin('*form[id="shippingPaymentForm"]', 'ckoCheckoutPaymentGiropay');
})(jQuery, window);
