{if $no_nettodisplay != 1}
  {if $price_onlynetto != 1}
    {if $p.TaxValue}
      {$lang.Shop_info_incl_vat_short|replace: "__PERCENT__": $p.TaxValue}
    {else}
      {$lang.Shop_info_incl_vat_short|replace: "__PERCENT__": $TaxValue}
    {/if}
  {/if}
  {if $price_onlynetto == 1 && !empty($p.price_ust_ex)}
    {if $p.TaxValue}
      {$lang.Shop_info_excl_vat_short|replace: "__PERCENT__": $p.TaxValue}
    {else}
      {$lang.Shop_info_excl_vat_short|replace: "__PERCENT__": $TaxValue}={$p.price_ust_ex|numformat} {$currency_symbol}
    {/if}
  {/if}
{/if}
