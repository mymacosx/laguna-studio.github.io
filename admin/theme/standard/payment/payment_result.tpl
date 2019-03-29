<br />
{if $payment_false == 1}
  {#Payment_Failed#}
{elseif $payment_false == 2}
  {#Payment_Wait#}
{else}
  {#Payment_Succes#}
{/if}
