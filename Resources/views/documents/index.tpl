{extends file="parent:documents/index.tpl"}

{* Checkout.com additional information *}
{block name="document_index_address"}
    {$smarty.block.parent}

    {if $ckoShowAdditionalInformation}
        {eval var=$ckoCustomDocumentElements.CkoCheckoutPayment_Address.value}
    {/if}
{/block}

{block name="document_index_info_dispatch"}
    {$smarty.block.parent}

    {if $ckoShowAdditionalInformation}
        {eval var=$ckoCustomDocumentElements.CkoCheckoutPayment_Dispatch.value}
    {/if}
{/block}