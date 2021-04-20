;(function ($, window) {
    'use strict';

    $.plugin('ckoCheckoutPaymentKlarna', {
        defaults: {
            confirmActionsSelector: '.confirm--actions.table--actions',
            confirmActionsBottomSelector: '.confirm--actions.table--actions.block.actions--bottom',
            shippingPaymentFormSelector: '#shippingPaymentForm',
            confirmActionsSubmitButtonSelector: 'button[type="submit"]',
            klarnaSelector: '[data-cko-checkout-payment-klarna="true"]',
            klarnaNotAvailableCommunicationMessageSelector: '.cko-klarna-not-available-communication-message',
            klarnaSavePaymentDataIdentifier: 'cko-checkout-payment-klarna-request-url',
            klarnaCheckBasketSignatureIdentifier: 'cko-checkout-payment-klarna-check-signature-url',
            klarnaExternalJsUrl: 'https://x.klarnacdn.net/kp/lib/v1/api.js'
        },

        $shippingPaymentForm: null,
        $confirmActions: null,
        $confirmActionsSubmitButtonTop: null,
        $confirmActionsSubmitButtonBottom: null,

        $klarna: null,
        klarnaClientToken: null,
        klarnaInstanceId: null,
        klarnaPaymentMethods: null,
        klarnaData: null,
        klarnaSavePaymentDataRequestUrl: null,
        klarnaCheckBasketSignatureUrl: null,

        init: function () {
            this.$shippingPaymentForm = $(this.opts.shippingPaymentFormSelector);
            this.$confirmActions = this.$shippingPaymentForm.find(this.opts.confirmActionsSelector);
            this.$confirmActionsSubmitButtonTop = this.$confirmActions.not('.actions--bottom').find(this.opts.confirmActionsSubmitButtonSelector);
            this.$confirmActionsSubmitButtonBottom = $(this.opts.confirmActionsBottomSelector).find(this.opts.confirmActionsSubmitButtonSelector);
            this.$klarnaNotAvailableCommunicationMessage = this.$shippingPaymentForm.find(this.opts.klarnaNotAvailableCommunicationMessageSelector);

            this.$klarna = this.$shippingPaymentForm.find(this.opts.klarnaSelector);
            this.klarnaClientToken = this.$klarna.data('cko-checkout-payment-klarna-client-token');
            this.klarnaInstanceId = this.$klarna.data('cko-checkout-payment-klarna-instance-id');
            this.klarnaPaymentMethods = this.$klarna.data('cko-checkout-payment-klarna-payment-methods');
            this.klarnaData = this.$klarna.data('cko-checkout-payment-klarna-data');
            this.klarnaSavePaymentDataRequestUrl = this.$klarna.data(this.opts.klarnaSavePaymentDataIdentifier) || null;
            this.klarnaCheckBasketSignatureUrl = this.$klarna.data(this.opts.klarnaCheckBasketSignatureIdentifier) || null;

            this.registerEventListeners();

            if(typeof Klarna !== 'undefined') {
                this.loadForm(this.klarnaData);
            } else {
                $.getScript(this.opts.klarnaExternalJsUrl, $.proxy(this.loadForm, this, this.klarnaData));
            }

            $.publish('plugin/checkoutPaymentKlarna/init', this);
        },

        registerEventListeners: function () {
            $.subscribe('plugin/swShippingPayment/onInputChanged', $.proxy(this.onPaymentMethodChange, this));
            this.$confirmActionsSubmitButtonTop.on('click', $.proxy(this.onSubmitShippingPaymentForm, this));
            this.$confirmActionsSubmitButtonBottom.on('click', $.proxy(this.onSubmitShippingPaymentForm, this));

            $.publish('plugin/ckoCheckoutPaymentKlarna/registerEventListeners', this);
        },

        loadForm: function (klarnaData) {
            var me = this;

            try {
                Klarna.Payments.init({
                        klarnaPaymentIdentifierPrefix: 'checkout-payment-klarna',
                        client_token: me.klarnaClientToken
                    }
                );

                Klarna.Payments.load({
                        container: "#klarna_container",
                        payment_method_categories: me.klarnaPaymentMethods,
                        instance_id: me.klarnaInstanceId
                    },
                    // data
                    klarnaData,

                    // callback
                    function (response) {
                        if (!response.show_form) {
                            me.showNotAvailableCommunicationMessage();
                            me.deactivateConfirmActionButton();
                        } else {
                            me.hideNotAvailableCommunicationMessage();
                            me.activateConfirmActionButton();
                        }
                    }
                );

            } catch (e) {
                me.showNotAvailableCommunicationMessage();
                me.deactivateConfirmActionButton();
            }
        },

        saveKlarnaToken: function (token, callbackAfterSuccess) {
            $.ajax({
                url: this.klarnaSavePaymentDataRequestUrl,
                method: 'POST',
                cache: false,
                data: {
                    ckoToken: token
                }
            }).done(function () {
                $.publish('plugin/ckoCheckoutPaymentKlarna/saveKlarnaToken/done', this, event);

                callbackAfterSuccess();
            }).fail(function () {
                $.publish('plugin/ckoCheckoutPaymentKlarna/saveKlarnaToken/failed', this, event);
            });
        },

        checkBasketSignature: function (callbackAfterSuccess) {
            $.ajax({
                url: this.klarnaCheckBasketSignatureUrl,
                method: 'POST',
                cache: false,
                data: {
                    currentBillingAddress: this.klarnaData.billing_address
                }
            }).done(function (response) {
                $.publish('plugin/ckoCheckoutPaymentKlarna/checkBasketSignature/done', this, event);

                callbackAfterSuccess(response);
            }).fail(function () {
                $.publish('plugin/ckoCheckoutPaymentKlarna/checkBasketSignature/failed', this, event);
            });
        },

        showNotAvailableCommunicationMessage: function () {
            this.$klarnaNotAvailableCommunicationMessage.removeClass('is--hidden');
        },

        hideNotAvailableCommunicationMessage: function () {
            this.$klarnaNotAvailableCommunicationMessage.addClass('is--hidden');
        },

        activateConfirmActionButton: function () {
            this.$confirmActionsSubmitButtonTop.removeClass('is--disabled');
            this.$confirmActionsSubmitButtonBottom.removeClass('is--disabled');

            this.$confirmActionsSubmitButtonTop.removeAttr('disabled');
            this.$confirmActionsSubmitButtonBottom.removeAttr('disabled');
        },

        deactivateConfirmActionButton: function () {
            this.$confirmActionsSubmitButtonTop.addClass('is--disabled');
            this.$confirmActionsSubmitButtonBottom.addClass('is--disabled');

            this.$confirmActionsSubmitButtonTop.attr('disabled', 'disabled');
            this.$confirmActionsSubmitButtonBottom.attr('disabled', 'disabled');
        },

        onSubmitShippingPaymentForm: function (e) {
            e.preventDefault();

            var me = this;

            try {
                Klarna.Payments.authorize({
                        instance_id: me.klarnaInstanceId,
                        auto_finalize: false
                    },
                    {},
                    function (response) {
                        if (!response.approved || !response.authorization_token) {
                            $.publish('plugin/ckoCheckoutPaymentKlarna/onSubmitShippingPaymentForm/authorize/failed', me, response);

                            return;
                        }

                        me.checkBasketSignature(function (response) {
                            // reload the form if the signature has changed since the first auth
                            if (!response.isBasketSignatureValid) {
                                me.loadForm(response.klarnaData);
                            }
                        });

                        $.publish('plugin/ckoCheckoutPaymentKlarna/onSubmitShippingPaymentForm/authorize/done', me, response);

                        me.saveKlarnaToken(response.authorization_token, function () {
                            e.preventDefault();
                            me.$shippingPaymentForm.submit();
                        });
                    }
                )
            } catch (error) {
                $.publish('plugin/ckoCheckoutPaymentKlarna/onSubmitShippingPaymentForm/authorize/failed', me, error);
            }
        },

        onPaymentMethodChange: function () {
            // we need to reinitialize payment handler on payment method change
            window.StateManager.addPlugin('*[data-cko-checkout-payment-klarna="true"]', 'ckoCheckoutPaymentKlarna');

            $.publish('plugin/ckoCheckoutPaymentKlarna/onPaymentMethodInputChange', this);
        }
    });

    window.StateManager.addPlugin('*[data-cko-checkout-payment-klarna="true"]', 'ckoCheckoutPaymentKlarna');
})(jQuery, window);
