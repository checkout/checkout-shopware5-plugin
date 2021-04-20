{extends file="parent:frontend/checkout/finish.tpl"}

{* Checkout.com additional information *}
{block name="frontend_checkout_finish_dispatch_method"}
    {$smarty.block.parent}

    {include file="frontend/cko_checkout_payment/cko_sepa/checkout/mandate_information.tpl"}
{/block}