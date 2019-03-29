{if !empty($price_group)}
  <br />
  <small><strong>{#Shop_Price_Group#}</strong></small>
  <br />
  {foreach from=$price_group item=pg}
    <small><strong>{$pg->Name}</strong>: {$pg->Rabatt} % {#Shop_dicount_d#}, {#Shop_f_price_s#} - {$pg->price|numformat} {$currency_symbol}</small>
    <br />
  {/foreach}
{/if}

{if $st_prices}
  <br />
  <small><strong>{#Shop_volume_discounts_d#}</strong></small>
  <br />
  {foreach from=$st_prices item=st name=sta}
    {if $smarty.foreach.sta.last}
      <small>{#Shop_discount_endf#} {$st->Von}</small>
    {else}
      <small>{$st->Von} - {$st->Bis}</small>
    {/if}
    <small>{#Shop_pieces#}: {$st->Wert|numf} {if $st->Operand == 'pro'}%{else}{$currency_symbol}{/if}&nbsp;&nbsp;{#Shop_dicount_d#}</small>
    <br />
  {/foreach}
{/if}
