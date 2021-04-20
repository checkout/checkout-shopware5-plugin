{extends file="parent:frontend/checkout/error_messages.tpl"}

{block name="frontend_checkout_error_messages_basket_error"}
    {$smarty.block.parent}

    {block name="frontend_checkout_cko_checkout_payment_payment_declined_error_message"}
        {if $ckoHasPaymentFailed}
            {include file="frontend/_includes/messages.tpl" type="error" content="{s name="payment/declined"}{/s}"}

            {* set response code errors here *}

        {/if}
    {/block}
{/block}
