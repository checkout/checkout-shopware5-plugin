;(function ($, window) {
    'use strict';

    $.plugin('ckoCheckoutPaymentSepa', {
        defaults: {
            shippingPaymentFormSelector: '#shippingPaymentForm',
            sepaFormSelector: '[data-cko-checkout-payment-sepa="true"]',
            sepaIbanSelector: '#cko-sepa-iban-field',
            sepaBicSelector: '#cko-sepa-bic-field',
            sepaAcceptMandateSelector: '#cko-sepa-accept-mandate-field',
            sepaSavePaymentDataIdentifier: 'cko-checkout-payment-sepa-request-url'
        },

        $shippingPaymentForm: null,
        $sepaForm: null,

        $sepaIban: null,
        $sepaBic: null,
        $sepaAcceptMandate: null,

        shippingPaymentRequestUrl: null,
        sepaSavePaymentDataRequestUrl: null,

        init: function () {
            this.$shippingPaymentForm = $(this.opts.shippingPaymentFormSelector);
            this.$sepaForm = this.$shippingPaymentForm.find(this.opts.sepaFormSelector);

            this.$sepaIban = this.$sepaForm.find(this.opts.sepaIbanSelector);
            this.$sepaBic = this.$sepaForm.find(this.opts.sepaBicSelector);
            this.$sepaAcceptMandate = this.$sepaForm.find(this.opts.sepaAcceptMandateSelector);

            this.shippingPaymentRequestUrl = this.$shippingPaymentForm.attr('action') || null;
            this.sepaSavePaymentDataRequestUrl = this.$sepaForm.data(this.opts.sepaSavePaymentDataIdentifier) || null;

            this.registerEventListeners();

            $.publish('plugin/ckoCheckoutPaymentSepa/init', this);
        },

        registerEventListeners: function () {
            this.$sepaForm.on('change', $.proxy(this.onChangeSepaForm, this));

            $.subscribe('plugin/swShippingPayment/onInputChanged', $.proxy(this.onPaymentMethodChange, this));
            $.publish('plugin/ckoCheckoutPaymentSepa/registerEventListeners', this);
        },

        onChangeSepaForm: function (event) {
            if (!this.$sepaIban.val() || !this.$sepaAcceptMandate.is(':checked')) {
                return;
            }

            $.loadingIndicator.open({
                openOverlay: true,
                closeOnClick: false
            });

            $.ajax({
                url: this.sepaSavePaymentDataRequestUrl,
                method: 'POST',
                cache: false,
                data: {
                    ckoIban: this.$sepaIban.val(),
                    ckoBic: this.$sepaBic.val()
                }
            }).done(function () {
                $.publish('plugin/ckoCheckoutPaymentSepa/onChangeSepaForm/done', this, event);

                $.loadingIndicator.close();
            }).fail(function () {
                $.publish('plugin/ckoCheckoutPaymentSepa/onChangeSepaForm/failed', this, event);

                $.loadingIndicator.close();
            });
        },

        onPaymentMethodChange: function () {
            // we need to reinitialize payment handler on payment method change
            window.StateManager.addPlugin('*form[id="shippingPaymentForm"]', 'ckoCheckoutPaymentSepa');

            $.publish('plugin/ckoCheckoutPaymentSepa/onPaymentMethodInputChange', this);
        }
    });

    window.StateManager.addPlugin('*form[id="shippingPaymentForm"]', 'ckoCheckoutPaymentSepa');
})(jQuery, window);
