;(function ($, window) {
    'use strict';

    $.plugin('ckoCheckoutPaymentBase', {
        defaults: {
            shippingPaymentFormSelector: '#shippingPaymentForm',
            googlePayPaymentSelector: '.is--cko-google-pay-payment',
            applePayPaymentSelector: '.is--cko-apple-pay-payment',
            klarnaPaymentSelector: '.is--cko-klarna-pay-payment'
        },

        $shippingPaymentForm: null,

        init: function () {
            this.$shippingPaymentForm = $(this.opts.shippingPaymentFormSelector);

            this.registerEventListeners();

            $.publish('plugin/ckoCheckoutPaymentBase/init', this);
        },

        registerEventListeners: function () {
            $.subscribe('plugin/swShippingPayment/onInputChanged', $.proxy(this.onPaymentMethodChange, this));
            $.publish('plugin/ckoCheckoutPaymentBase/registerEventListeners', this);
        },

        onPaymentMethodChange: function () {
            var $googlePayPayment = $(this.opts.googlePayPaymentSelector).parent().find('.method--input > input.radio'),
                $applePayPayment = $(this.opts.applePayPaymentSelector).parent().find('.method--input > input.radio'),
                $klarnaPayment = $(this.opts.klarnaPaymentSelector).parent().find('.method--input > input.radio');

            // we need to reinitialize some payment methods if they are selected
            // so they will be triggered again

            if ($googlePayPayment.is(':checked')) {
                window.StateManager.addPlugin('*[data-cko-checkout-payment-google-pay="true"]', 'ckoCheckoutPaymentGooglePay');
            }

            if ($applePayPayment.is(':checked')) {
                window.StateManager.addPlugin('*[data-cko-checkout-payment-apple-pay="true"]', 'ckoCheckoutPaymentApplePay');
            }

            if ($klarnaPayment.is(':checked')) {
                window.StateManager.addPlugin('*[data-cko-checkout-payment-klarna="true"]', 'ckoCheckoutPaymentKlarna');
            }

            $.publish('plugin/ckoCheckoutPaymentBase/onPaymentMethodInputChange', this);
        }
    });

    window.StateManager.addPlugin('*form[id="shippingPaymentForm"]', 'ckoCheckoutPaymentBase');
})(jQuery, window);