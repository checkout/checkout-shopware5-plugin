{block name="frontend_checkout_payment_cko_checkout_payment_credit_card_container"}

	{if {config name=enableSaveCardOption} && !$ckoIsGuestOrder}
		{foreach from=$ckoCreditcardSaved item=card}
			<div data-cko-creditcard-source-id="{$card->getSourceId()}">
			<fieldset>
				<div><strong>{s name="label/cardNumber"}{/s}:</strong> XXXX-XXXX-XXXX-{$card->getLastFour()}</div>
				<div><strong>{s name="label/expiryDate"}{/s}:</strong> {$card->getExpiryMonth()}/{$card->getExpiryYear()}</div>
			</fieldset>
			<fieldset>
				{if $card->getSourceId() === $ckoSourceId}
					<button class="btn is--primary" disabled="disabled">{s name="button/selected"}Selected{/s}</button>
				{else}
					<button class="btn is--primary" data-cko-creditcard-select-source-id="{$card->getSourceId()}">{s name="button/select"}Select{/s}</button>
					<button class="btn is--primary" data-cko-creditcard-delete-source-id="{$card->getSourceId()}">{s name="button/delete"}Delete{/s}</button>
				{/if}
			</fieldset>
			</div>
		{/foreach}
	{/if}

    <div id="cko_creditcard_display"{if !$ckoCreditcardIsEntered} class="is--hidden"{/if}>
        <fieldset>
			<div><strong>{s name="label/cardNumber"}{/s}:</strong> XXXX-XXXX-XXXX-<span id="cko_creditcard_display_last4">{$ckoCreditcardLast4}</span></div>
			<div><strong>{s name="label/expiryDate"}{/s}:</strong> <span id="cko_creditcard_display_date">{$ckoCreditcardExpiryDate}</span>
        </fieldset>
		<fieldset>
			<button id="cko_creditcard_display_button" class="btn is--primary">{s name="button/edit"}Change{/s}</button>
		</fieldset>
    </div>

    <div id="cko_creditcard"{if $ckoCreditcardIsEntered} class="is--hidden"{/if}
         data-cko-public-key="{$ckoApiPublicKey}"
         data-cko-payment-data-request-url="{url controller=CkoCheckoutPaymentCreditcard action=savePaymentData module=frontend}"
         data-cko-select-source-request-url="{url controller=CkoCheckoutPaymentCreditcard action=selectSource module=frontend}"
         data-cko-delete-source-request-url="{url controller=CkoCheckoutPaymentCreditcard action=deleteSource module=frontend}"
    >

        <form id="cko_creditcard_form" class="cko-form" method="POST" action="#">
			<fieldset>
				<div id="cko_new_card">
					{s name="label/newCard"}New card:{/s}
				</div>
			</fieldset>
            <fieldset>
				<label for="card-number">{s name="label/cardNumber"}{/s}</label>
				<div class="input-container card-number">
					<div class="icon-container">
						<img id="icon-card-number"
							 src="{link file='frontend/_public/src/img/card-icons/card.svg' fullPath=true}" alt="PAN"/>
					</div>
					<div class="card-number-frame"></div>
					<div class="icon-container icon-error">
						<img id="icon-card-number-error"
							 src="{link file='frontend/_public/src/img/card-icons/error.svg' fullPath=true}"/>
					</div>
					<div class="icon-container payment-method">
						<img id="logo-payment-method"/>
					</div>
				</div>
            </fieldset>

            <fieldset class="date-and-code">
                <div>
                    <label for="expiry-date">{s name="label/expiryDate"}{/s}</label>
                    <div class="input-container expiry-date">
                        <div class="icon-container">
                            <img id="icon-expiry-date"
                                 src="{link file='frontend/_public/src/img/card-icons/exp-date.svg' fullPath=true}"
                                 alt="Expiry date"/>
                        </div>
                        <div class="expiry-date-frame"></div>
                        <div class="icon-container icon-error">
                            <img id="icon-expiry-date-error"
                                 src="{link file='frontend/_public/src/img/card-icons/error.svg' fullPath=true}"/>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="cvv">{s name="label/securityCode"}{/s}</label>
                    <div class="input-container cvv">
                        <div class="icon-container">
                            <img id="icon-cvv"
                                 src="{link file='frontend/_public/src/img/card-icons/cvv.svg' fullPath=true}"
                                 alt="CVV"/>
                        </div>
                        <div class="cvv-frame"></div>
                        <div class="icon-container icon-error">
                            <img id="icon-cvv-error"
                                 src="{link file='frontend/_public/src/img/card-icons/error.svg' fullPath=true}"/>
                        </div>
                    </div>
                </div>
            </fieldset>
			{if {config name=enableSaveCardOption} && !$ckoIsGuestOrder }
				<fieldset>
					<input id="cko_creditcard_form_save" name="ckoCreditcardSaveCard" type="checkbox">
					<label for="cko_creditcard_form_save">{s name="label/saveCard"}{/s}</label>
				</fieldset>
			{/if}
            <fieldset>
                <button id="cko_creditcard_form_button" class="btn is--primary" disabled="disabled">OK</button>
            </fieldset>
        </form>

    </div>
{/block}
