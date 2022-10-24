;(function ($, window) {
    'use strict';

    $.plugin('ckoCheckoutPaymentApplePay', {
        defaults: {
            confirmActionsSelector: '.confirm--actions.table--actions',
            confirmActionsBottomSelector: '.confirm--actions.table--actions.block.actions--bottom',
            shippingPaymentFormSelector: '#shippingPaymentForm',
            confirmActionsSubmitButtonSelector: 'button[type="submit"]',
            applePayPaymentIdentifierPrefix: 'cko-checkout-payment-apple-pay',
            applePayFormSelector: '[data-cko-checkout-payment-apple-pay="true"]',
            applePayButtonSelector: '#cko-apple-pay-button',
            applePayNotAvailableCommunicationMessageSelector: '.cko-apple-pay-not-available-communication-message',
            applePayVersion: 5
        },

        $shippingPaymentForm: null,
        $confirmActions: null,
        $confirmActionsSubmitButtonTop: null,
        $confirmActionsSubmitButtonBottom: null,

        $applePayForm: null,
        $applePayButton: null,

        applePayVersion: null,

        savePaymentDataRequestUrl: null,
        merchantValidationRequestUrl: null,

        merchantId: null,
        supportedNetworks: '',
        merchantCapabilities: '',
        currentCurrency: null,
        countryCode: null,
        shopName: null,
        totalPrice: null,

        init: function () {
            this.$shippingPaymentForm = $(this.opts.shippingPaymentFormSelector);
            this.$confirmActions = this.$shippingPaymentForm.find(this.opts.confirmActionsSelector);
            this.$confirmActionsSubmitButtonTop = this.$confirmActions.not('.actions--bottom').find(this.opts.confirmActionsSubmitButtonSelector);
            this.$confirmActionsSubmitButtonBottom = $(this.opts.confirmActionsBottomSelector).find(this.opts.confirmActionsSubmitButtonSelector);
            this.$applePayNotAvailableCommunicationMessage = this.$shippingPaymentForm.find(this.opts.applePayNotAvailableCommunicationMessageSelector);

            this.$applePayForm = this.$shippingPaymentForm.find(this.opts.applePayFormSelector);
            this.$applePayButton = $(this.opts.applePayButtonSelector);

            this.applePayVersion = this.opts.applePayVersion;

            this.savePaymentDataRequestUrl = this.$applePayForm.data(this.opts.applePayPaymentIdentifierPrefix + '-request-url') || null;
            this.merchantValidationRequestUrl = this.$applePayForm.data(this.opts.applePayPaymentIdentifierPrefix + '-merchant-validation-request-url') || null;

            this.merchantId = this.$applePayForm.data(this.opts.applePayPaymentIdentifierPrefix + '-merchant-id') || null;
            this.supportedNetworks = this.$applePayForm.data(this.opts.applePayPaymentIdentifierPrefix + '-supported-networks') || '';
            this.merchantCapabilities = this.$applePayForm.data(this.opts.applePayPaymentIdentifierPrefix + '-merchant-capabilities') || '';
            this.currentCurrency = this.$applePayForm.data(this.opts.applePayPaymentIdentifierPrefix + '-currency') || null;
            this.countryCode = this.$applePayForm.data(this.opts.applePayPaymentIdentifierPrefix + '-country-code') || null;
            this.shopName = this.$applePayForm.data(this.opts.applePayPaymentIdentifierPrefix + '-shop-name') || null;
            this.totalPrice = this.$applePayForm.data(this.opts.applePayPaymentIdentifierPrefix + '-total-price') || null;

            this.registerEventListeners();

            $.publish('plugin/ckoCheckoutPaymentApplePay/init', this);
        },

        registerEventListeners: function () {
            if (this.isApplePayAvailable()) {
                this.onApplePayLoaded();
            } else {
                this.showNotAvailableCommunicationMessage();
            }

            $(this.opts.applePayButtonSelector).on('click', $.proxy(this.onClickApplePayButton, this));
            $.publish('plugin/ckoCheckoutPaymentApplePay/registerEventListeners', this);
        },

        /**
         * @see {@link https://developer.apple.com/documentation/apple_pay_on_the_web/apple_pay_js_api/creating_an_apple_pay_session}
         */
        getApplePayPaymentRequest: function () {
            return {
                countryCode: this.countryCode,
                currencyCode: this.currentCurrency,
                supportedNetworks: this.supportedNetworks.split(',') || [],
                merchantCapabilities: this.merchantCapabilities.split(',') || [],
                total: {
                    label: this.shopName,
                    amount: this.totalPrice
                }
            };
        },

        isApplePayAvailable: function() {
            if (typeof window.ApplePaySession === 'undefined') {
                return false;
            }

            return ApplePaySession.supportsVersion(this.applePayVersion);
        },

        validateMerchant: function (validationUrl, afterSuccessCallback, afterErrorCallback) {
            $.ajax({
                url: this.merchantValidationRequestUrl,
                method: 'POST',
                cache: false,
                data: {
                    url: validationUrl
                }
            }).done(function (response) {
                if (typeof afterSuccessCallback === 'function') {
                    afterSuccessCallback(response);
                }

                $.publish('plugin/ckoCheckoutPaymentApplePay/validateMerchant/success', this, response);
            }).fail(function () {
                if (typeof afterErrorCallback === 'function') {
                    afterErrorCallback();
                }

                $.publish('plugin/ckoCheckoutPaymentApplePay/validateMerchant/error', this);
            });
        },

        processPayment: function (paymentData, afterSuccessCallback, afterErrorCallback) {
            $.publish('plugin/ckoCheckoutPaymentApplePay/processPayment/before', this);

            $.ajax({
                url: this.savePaymentDataRequestUrl,
                method: 'POST',
                cache: false,
                data: {
                    ckoApplePayTransactionId: paymentData.header.transactionId,
                    ckoApplePayPublicKeyHash: paymentData.header.publicKeyHash,
                    ckoEphemeralPublicKey: paymentData.header.ephemeralPublicKey,
                    ckoApplePayVersion: paymentData.version,
                    ckoApplePaySignature: paymentData.signature,
                    ckoApplePayData: paymentData.data
                }
            }).done(function (response) {
                if (typeof afterSuccessCallback === 'function') {
                    afterSuccessCallback(response);
                }

                $.publish('plugin/ckoCheckoutPaymentApplePay/processPayment/success', this, response);
            }).fail(function () {
                if (typeof afterErrorCallback === 'function') {
                    afterErrorCallback();
                }

                $.publish('plugin/ckoCheckoutPaymentApplePay/processPayment/error', this);
            });

            $.publish('plugin/ckoCheckoutPaymentApplePay/processPayment/after', this);
        },

        /**
         * @see {@link https://developer.apple.com/documentation/apple_pay_on_the_web/apple_pay_js_api/creating_an_apple_pay_session}
         */
        createApplePaySession: function () {
            var me = this,
                applePaySession = new ApplePaySession(this.applePayVersion, this.getApplePayPaymentRequest());

            $.publish('plugin/ckoCheckoutPaymentApplePay/createApplePaySession/before', this, applePaySession);

            applePaySession.onvalidatemerchant = function (event) {
                me.validateMerchant(event.validationURL, function (response) {
                    if (!response.success) {
                        me.showNotAvailableCommunicationMessage();

                        return;
                    }

                    applePaySession.completeMerchantValidation(response.merchantSession);
                }, function () {
                    me.showNotAvailableCommunicationMessage();
                });
            }

            applePaySession.onshippingcontactselected = function () {
                var options = {
                    type: 'final',
                    label: me.shopName,
                    amount: me.totalPrice
                };

                applePaySession.completeShippingContactSelection(ApplePaySession.STATUS_SUCCESS, [], options, []);
            }

            applePaySession.onshippingmethodselected = function () {
                var options = {
                    type: 'final',
                    label: me.shopName,
                    amount: me.totalPrice
                };

                applePaySession.completeShippingMethodSelection(ApplePaySession.STATUS_SUCCESS, options, []);
            }

            applePaySession.onpaymentmethodselected = function () {
                var options = {
                    type: 'final',
                    label: me.shopName,
                    amount: me.totalPrice
                };

                applePaySession.completePaymentMethodSelection(options, []);
            }

            applePaySession.onpaymentauthorized = function (event) {
                me.processPayment(event.payment.token.paymentData, function (response) {
                    if (!response.success) {
                        applePaySession.completePayment(ApplePaySession.STATUS_FAILURE);
                        me.showNotAvailableCommunicationMessage();

                        return;
                    }

                    applePaySession.completePayment(ApplePaySession.STATUS_SUCCESS);
                    me.$shippingPaymentForm.submit();
                }, function () {
                    applePaySession.completePayment(ApplePaySession.STATUS_FAILURE);
                    me.showNotAvailableCommunicationMessage();
                });
            }

            applePaySession.begin();

            $.publish('plugin/ckoCheckoutPaymentApplePay/createApplePaySession/after', this, applePaySession);
        },

        showNotAvailableCommunicationMessage: function () {
            this.$applePayNotAvailableCommunicationMessage.removeClass('is--hidden');
            this.deactivateConfirmActionButton();
        },

        hideNotAvailableCommunicationMessage: function () {
            this.$applePayNotAvailableCommunicationMessage.addClass('is--hidden');
            this.activateConfirmActionButton();
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

        onClickApplePayButton: function () {
            $.publish('plugin/ckoCheckoutPaymentApplePay/onClickApplePayButton', this);

            this.createApplePaySession();
        },

        onApplePayLoaded: function () {
            var me = this;

            $.publish('plugin/ckoCheckoutPaymentApplePay/onApplePayLoaded/before', this);

            ApplePaySession.canMakePaymentsWithActiveCard(this.merchantId).then(function (canMakePayments) {
               if (canMakePayments) {
                   me.$applePayButton.removeClass('is--hidden');

                   $.publish('plugin/ckoCheckoutPaymentApplePay/onApplePayLoaded/ready', me, canMakePayments);

                   return;
               }

                me.showNotAvailableCommunicationMessage();
            }).catch(function (error) {
                me.showNotAvailableCommunicationMessage();

                $.publish('plugin/ckoCheckoutPaymentApplePay/onApplePayLoaded/error', me, error);
            });

            $.publish('plugin/ckoCheckoutPaymentApplePay/onApplePayLoaded/after', this);
        }
    });

    window.StateManager.addPlugin('*[data-cko-checkout-payment-apple-pay="true"]', 'ckoCheckoutPaymentApplePay');
})(jQuery, window);
