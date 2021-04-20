{extends file="parent:frontend/checkout/change_payment.tpl"}

{* Checkout.com Payment Methods *}
{block name="frontend_checkout_payment_fieldset_input_label"}
    {assign var=isCkoPaymentMethod value=$payment_mean.name|substr:0:4 == $ckoPaymentMethodPrefix}

    {* Checkout.com Payment Method *}
    {if $isCkoPaymentMethod}
        {assign var=ckoPaymentMethodInputLabelTemplate value="frontend/cko_checkout_payment/{$payment_mean.name}/input_label.tpl"}

        {if $ckoPaymentMethodInputLabelTemplate|template_exists}
            {include file=$ckoPaymentMethodInputLabelTemplate}
        {else}
            {$smarty.block.parent}
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="frontend_checkout_payment_fieldset_description"}
    {assign var=isCkoPaymentMethod value=$payment_mean.name|substr:0:4 == $ckoPaymentMethodPrefix}

    {* Checkout.com Payment Method *}
    {if $isCkoPaymentMethod}
        {assign var=ckoPaymentMethodTemplate value="frontend/cko_checkout_payment/{$payment_mean.name}/payment_method.tpl"}

        {if $ckoPaymentMethodTemplate|template_exists}
            {include file=$ckoPaymentMethodTemplate}
        {else}
            {$smarty.block.parent}
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}
