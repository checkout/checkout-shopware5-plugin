{block name="frontend_checkout_payment_cko_checkout_payment_sepa_checkout_mandate_information"}
    {if $ckoShowAdditionalSepaInformation}
        <br />

        {block name="frontend_checkout_payment_cko_checkout_payment_sepa_checkout_mandate_information_creditor_name"}
            <strong>{s name="creditor"}{/s}</strong> {$ckoSepaMandateCreditorName}<br />
        {/block}

        {block name="frontend_checkout_payment_cko_checkout_payment_sepa_checkout_mandate_information_creditor_address_first"}
            <span>{$ckoSepaMandateCreditorAddressFirst}</span><br />
        {/block}

        {block name="frontend_checkout_payment_cko_checkout_payment_sepa_checkout_mandate_information_creditor_address_second"}
            <span>{$ckoSepaMandateCreditorAddressSecond}</span><br />
        {/block}

        {block name="frontend_checkout_payment_cko_checkout_payment_sepa_checkout_mandate_information_creditor_country"}
            <span>{$ckoSepaMandateCreditorCountry}</span><br />
        {/block}

        {block name="frontend_checkout_payment_cko_checkout_payment_sepa_checkout_mandate_information_creditor_id"}
            <strong>{s name="creditorId"}{/s}</strong> {$ckoSepaMandateCreditorId}<br />
        {/block}

        {block name="frontend_checkout_payment_cko_checkout_payment_sepa_checkout_mandate_information_mandate_reference"}
            {if $ckoSepaMandateReference}
                <strong>{s name="mandateReference"}{/s}</strong> {$ckoSepaMandateReference}<br />
            {/if}
        {/block}

        {block name="frontend_checkout_payment_cko_checkout_payment_sepa_checkout_mandate_information_estimated_due_date"}
            {if $ckoSepaEstimatedDueDate}
                <strong>{s name="estimatedDueDate"}{/s}</strong> {$ckoSepaEstimatedDueDate}<br />
            {/if}
        {/block}
    {/if}
{/block}