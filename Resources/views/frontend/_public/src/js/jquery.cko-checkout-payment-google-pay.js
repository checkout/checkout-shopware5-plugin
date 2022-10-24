;(function ($, window) {
    'use strict';

    $.plugin('ckoCheckoutPaymentGooglePay', {
        defaults: {
            shippingPaymentFormSelector: '#shippingPaymentForm',
            googlePayPaymentIdentifierPrefix: 'cko-checkout-payment-google-pay',
            googlePayFormSelector: '[data-cko-checkout-payment-google-pay="true"]',
            googlePayButtonSelector: '#cko-google-pay-button',
            googlePayExternalJsUrl: 'https://pay.google.com/gp/p/js/pay.js'
        },

        $shippingPaymentForm: null,

        $googlePayForm: null,
        $googlePayButton: null,
        buttonColor: 'default',

        savePaymentDataRequestUrl: null,
        allowedCardNetworks: '',
        merchantId: null,
        gatewayMerchantId: null,
        environment: null,
        currentCurrency: null,
        totalPrice: null,
        googleClient: null,

        allowedPaymentMethods: ['CARD', 'TOKENIZED_CARD'],

        init: function () {
            this.$shippingPaymentForm = $(this.opts.shippingPaymentFormSelector);

            this.$googlePayForm = this.$shippingPaymentForm.find(this.opts.googlePayFormSelector);
            this.$googlePayButton = $(this.opts.googlePayButtonSelector);
            this.$googlePayButtonContainer = $(this.opts.googlePayButtonSelector + '-container');
            this.buttonColor = this.$googlePayForm.data(this.opts.googlePayPaymentIdentifierPrefix + '-button-color') || 'default';

            this.savePaymentDataRequestUrl = this.$googlePayForm.data(this.opts.googlePayPaymentIdentifierPrefix + '-request-url') || null;
            this.allowedCardNetworks = this.$googlePayForm.data(this.opts.googlePayPaymentIdentifierPrefix + '-allowed-card-networks') || '';
            this.merchantId = this.$googlePayForm.data(this.opts.googlePayPaymentIdentifierPrefix + '-merchant-id') || null;
            this.gatewayMerchantId = this.$googlePayForm.data(this.opts.googlePayPaymentIdentifierPrefix + '-gateway-merchant-id') || null;
            this.environment = this.$googlePayForm.data(this.opts.googlePayPaymentIdentifierPrefix + '-environment') || null;
            this.currentCurrency = this.$googlePayForm.data(this.opts.googlePayPaymentIdentifierPrefix + '-currency') || null;
            this.totalPrice = this.$googlePayForm.data(this.opts.googlePayPaymentIdentifierPrefix + '-total-price') || null;
            this.registerEventListeners();

            $.publish('plugin/ckoCheckoutPaymentGooglePay/init', this);
        },

        registerEventListeners: function () {
            if(typeof google !== 'undefined') {
                this.onGooglePayLoaded();
            } else {
                $.getScript(this.opts.googlePayExternalJsUrl, $.proxy(this.onGooglePayLoaded, this));
            }

            $(this.opts.googlePayButtonSelector).on('click', $.proxy(this.onClickGooglePayButton, this));
            $.publish('plugin/ckoCheckoutPaymentGooglePay/registerEventListeners', this);
        },

        /**
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#PaymentDataRequest|PaymentDataRequest}
         * @returns {object} PaymentDataRequest fields
         */
        getGooglePaymentDataRequest: function () {
            var tokenizationParameters = {
                tokenizationType: 'PAYMENT_GATEWAY',
                parameters: {
                    'gateway': 'checkoutltd',
                    'gatewayMerchantId': this.gatewayMerchantId
                }
            };

            return {
                merchantId: this.merchantId,
                paymentMethodTokenizationParameters: tokenizationParameters,
                allowedPaymentMethods: this.allowedPaymentMethods,
                cardRequirements: {
                    allowedCardNetworks: this.allowedCardNetworks.split(',')
                }
            };
        },

        /**
         * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#TransactionInfo|TransactionInfo}
         * @returns {object} transaction info, suitable for use as transactionInfo property of PaymentDataRequest
         */
        getGoogleTransactionInfo: function () {
            return {
                currencyCode: this.currentCurrency,
                totalPriceStatus: 'FINAL',
                totalPrice: parseFloat(this.totalPrice)
            };
        },

        /**
         * @see {@link https://developers.google.com/pay/api/web/reference/client#prefetchPaymentData|prefetchPaymentData()}
         */
        prefetchGooglePaymentData: function () {
            var paymentDataRequest = this.getGooglePaymentDataRequest();

            paymentDataRequest.transactionInfo = {
                totalPriceStatus: 'NOT_CURRENTLY_KNOWN',
                currencyCode: this.currentCurrency
            };

            this.googleClient.prefetchPaymentData(paymentDataRequest);

            $.publish('plugin/ckoCheckoutPaymentGooglePay/prefetchGooglePaymentData', this, paymentDataRequest);
        },

        processPayment: function (paymentData) {
            var me = this;

            $.loadingIndicator.open({
                openOverlay: true,
                closeOnClick: false
            });

            $.publish('plugin/ckoCheckoutPaymentGooglePay/processPayment/before', this);

            var parsedPaymentData = JSON.parse(paymentData.paymentMethodToken.token);

            $.ajax({
                url: this.savePaymentDataRequestUrl,
                method: 'POST',
                cache: false,
                data: {
                    ckoGooglePaySignature: parsedPaymentData.signature,
                    ckoGooglePayProtocolVersion: parsedPaymentData.protocolVersion,
                    ckoGooglePaySignedMessage: parsedPaymentData.signedMessage
                }
            }).done(function () {
                $.publish('plugin/ckoCheckoutPaymentGooglePay/processPayment/success', this, parsedPaymentData);

                $.loadingIndicator.close();
                me.$shippingPaymentForm.submit();
            }).fail(function () {
                $.publish('plugin/ckoCheckoutPaymentGooglePay/processPayment/error', this, parsedPaymentData);

                $.loadingIndicator.close();
                me.$shippingPaymentForm.submit();
            });

            $.publish('plugin/ckoCheckoutPaymentGooglePay/processPayment/after', this);
        },

        onClickGooglePayButton: function () {
            var me = this;

            var paymentDataRequest = me.getGooglePaymentDataRequest();
            paymentDataRequest.transactionInfo = me.getGoogleTransactionInfo();

            $.publish('plugin/ckoCheckoutPaymentGooglePay/onClickGooglePayButton/before', me, paymentDataRequest);

            this.googleClient.loadPaymentData(paymentDataRequest)
                .then(function (paymentData) {
                        // handle the response
                        me.processPayment(paymentData);
                    }
                ).catch(function (error) {
                    // show error in developer console for debugging
                    console.error('load payment data error', error);
                }
            );

            $.publish('plugin/ckoCheckoutPaymentGooglePay/onClickGooglePayButton/after', me, paymentDataRequest);
        },

        onGooglePayLoaded: function () {
            var me = this;
            me.googleClient = new google.payments.api.PaymentsClient({ environment: this.environment });
            $.publish('plugin/ckoCheckoutPaymentGooglePay/onGooglePayLoaded/before', this);

            const button = me.googleClient.createButton({
                buttonColor: me.buttonColor,
                buttonType: 'buy',
                onClick: function () { me.onClickGooglePayButton() },
            });
            me.$googlePayButtonContainer.append(button);


            me.googleClient.isReadyToPay({allowedPaymentMethods: this.allowedPaymentMethods})
                .then(function (response) {
                    $.publish('plugin/ckoCheckoutPaymentGooglePay/onGooglePayLoaded/success', me, response);

                    if (response.result) {
                        me.$googlePayButton.removeClass('is--hidden');
                        me.prefetchGooglePaymentData();

                        $.publish('plugin/ckoCheckoutPaymentGooglePay/onGooglePayLoaded/ready', me, response);
                    }
                }).catch(function (error) {
                // show error in developer console for debugging
                console.error('pre fetch error', error);

                $.publish('plugin/ckoCheckoutPaymentGooglePay/onGooglePayLoaded/error', me, error);
            });

            $.publish('plugin/ckoCheckoutPaymentGooglePay/onGooglePayLoaded/after', this);
        }
    });

    window.StateManager.addPlugin('*[data-cko-checkout-payment-google-pay="true"]', 'ckoCheckoutPaymentGooglePay');
})(jQuery, window);
