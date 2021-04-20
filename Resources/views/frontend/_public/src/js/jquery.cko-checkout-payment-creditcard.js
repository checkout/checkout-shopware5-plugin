;(function ($, window) {
	'use strict';

	$.plugin('ckoCheckoutPaymentCreditcard', {
		defaults: {},

		externalJs: "https://cdn.checkout.com/js/framesv2.min.js",
		$creditcardForm: null,
		$creditcardDisplay: null,
		$ckoPublicKey: null,
		creditcardSavePaymentDataRequestUrl: null,
		creditcardSelectSourceRequestUrl: null,
		creditcardDeleteSourceRequestUrl: null,

		init: function () {
			this.$creditcard = $('#cko_creditcard');
			this.$creditcardForm = $('#cko_creditcard_form');
			this.$creditcardDisplay = $('#cko_creditcard_display');
			this.$ckoPublicKey = this.$creditcard.data('cko-public-key')
			this.creditcardSavePaymentDataRequestUrl = this.$creditcard.data('cko-payment-data-request-url')
			this.creditcardSelectSourceRequestUrl = this.$creditcard.data('cko-select-source-request-url')
			this.creditcardDeleteSourceRequestUrl = this.$creditcard.data('cko-delete-source-request-url')

			if(typeof Frames !== 'undefined') {
				this.loadForm();
			}else {
				$.getScript(this.externalJs, $.proxy(this.loadForm, this))
			}

			$.publish('plugin/ckoCheckoutPaymentCreditcard/init', this);
		},

		loadForm: function () {
			var self = this;
			$('#cko_creditcard_form_button').click(function (event) {
				event.preventDefault();
				$.loadingIndicator.open({
					openOverlay: true,
					closeOnClick: false
				});
				Frames.submitCard();
			});

			$('#cko_creditcard_display_button').click(function (event) {
				event.preventDefault();
				Frames.enableSubmitForm();
				//TODO: maybe delete data from session?

				self.$creditcardDisplay.addClass('is--hidden');
				$('#cko_creditcard').removeClass('is--hidden');
			});

			$('[data-cko-creditcard-select-source-id]').click($.proxy(this.onSavedCardSelected, this));
			$('[data-cko-creditcard-delete-source-id]').click($.proxy(this.onSavedCardDeleted, this));

			Frames.init(this.$ckoPublicKey);
			Frames.addEventHandler(
				Frames.Events.CARD_VALIDATION_CHANGED,
				$.proxy(this.onCardvalidationChanged, this)
			);

			Frames.addEventHandler(
				Frames.Events.CARD_TOKENIZED,
				$.proxy(this.onCardTokenized, this)
			);

		},

		onSavedCardSelected: function (event) {
			event.preventDefault();
			var sourceId = $(event.target).data('cko-creditcard-select-source-id');

			$.loadingIndicator.open({
				openOverlay: true,
				closeOnClick: false
			});

			$.ajax({
				url: this.creditcardSelectSourceRequestUrl,
				method: 'POST',
				cache: false,
				data: {
					ckoSourceId: sourceId,
				}
			}).done(function () {
				$(event.target).attr('disabled', true);
				$.publish('plugin/ckoCheckoutPaymentCreditcard/onCreditcardSelected/done', this, event);
				$.loadingIndicator.close();
			}).fail(function () {
				$.loadingIndicator.close();
			});
		},

		onSavedCardDeleted: function (event) {
			event.preventDefault();
			var sourceId = $(event.target).data('cko-creditcard-delete-source-id');

			$.loadingIndicator.open({
				openOverlay: true,
				closeOnClick: false
			});

			$.ajax({
				url: this.creditcardDeleteSourceRequestUrl,
				method: 'POST',
				cache: false,
				data: {
					ckoSourceId: sourceId,
				}
			}).done(function () {
				$(event.target).attr('disabled', true);
				$.publish('plugin/ckoCheckoutPaymentCreditcard/onCreditcardDeleted/done', this, event);
				$('[data-cko-creditcard-source-id="' +  sourceId + '"]').remove();
				$.loadingIndicator.close();
			}).fail(function () {
				$.loadingIndicator.close();
			});
		},

		onCardTokenized: function (tokenData) {
			var token = tokenData.token;
			var expiryDate = tokenData.expiry_month + '/' + tokenData.expiry_year;
			var last4 = tokenData.last4;
			var saveCard = !!$('#cko_creditcard_form_save').is(':checked');

			this.$creditcardDisplay.find('#cko_creditcard_display_last4').text(last4);
			this.$creditcardDisplay.find('#cko_creditcard_display_date').text(expiryDate);

			this.$creditcardDisplay.removeClass('is--hidden');
			$('#cko_creditcard').addClass('is--hidden');

			$.ajax({
				url: this.creditcardSavePaymentDataRequestUrl,
				method: 'POST',
				cache: false,
				data: {
					ckoToken: token,
					ckoCreditcardExpiryDate: expiryDate,
					ckoCreditcardLast4: last4,
					ckoCreditcardSaveCard: saveCard,
				}
			}).done(function () {
				$.publish('plugin/checkoutPaymentCreditcard/onChangeCreditcardForm/done', this, tokenData);
				$.loadingIndicator.close();
			}).fail(function () {
				$.publish('plugin/checkoutPaymentCreditcard/onChangeCreditcardForm/failed', this, tokenData);
				$.loadingIndicator.close();
			});
		},

		onCardvalidationChanged: function (event) {
			$('#cko_creditcard_form_button').attr('disabled', !event.isValid);
		},

		onPaymentMethodChange: function () {
			// we need to reinitialize payment handler on payment method change
			window.StateManager.addPlugin('#cko_creditcard', 'checkoutPaymentCreditcard');
			$.publish('plugin/checkoutPaymentCreditcard/onPaymentMethodInputChange', this);
		}
	});
	$.subscribe('plugin/swShippingPayment/onInputChanged', $.proxy(function () {
		window.StateManager.addPlugin('#cko_creditcard', 'ckoCheckoutPaymentCreditcard');
	}, this));
	window.StateManager.addPlugin('#cko_creditcard', 'ckoCheckoutPaymentCreditcard');
})(jQuery, window);
