{if $shopsettings->PreiseGaeste == 1 || $loggedin}
  {if $p.Preis_Liste > 0}
    <strong>{#Shop_instead#}</strong>
    <span class="shop_price_old">
      <span id="price_list">{$p.Preis_Liste|numformat}</span> {$currency_symbol}
    </span>
    <span id="you_saved" style="display: none"></span>
    <br />
    <strong>{#Shop_usave#}</strong> {$p.diff|numformat} {$currency_symbol}&nbsp;({$p.diffpro|numformat}%)
    <br />
  {/if}
  <div class="shop_price_detail">
    {if $shopsettings->PreiseGaeste == 1 || $loggedin}
      {if $p.Preis > 0}
        <span id="new_price">{$p.Preis|numformat}</span> {$currency_symbol}
      {else}
        <span>{#Zvonite#}</span>
      {/if}
    {else}
      <strong>{#Shop_prices_justforUsers#}</strong>
    {/if}
  </div>
  {if $p.Preis > 0}
    {include file="$incpath/shop/tax_inf_small.tpl"}
  {/if}
  <br />
  {if $price_onlynetto != 1}
    {include file="$incpath/shop/i_price_detail_netto.tpl"}
  {elseif $price_onlynetto == 1 && !empty($p.price_ust_ex)}
    {include file="$incpath/shop/i_price_detail.tpl"}
  {else}
    {include file="$incpath/shop/i_price_detail_novat.tpl"}
  {/if}
  {include file="$incpath/shop/product_volumes.tpl"}
{else}
  <strong>{#Shop_prices_justforUsers#}</strong>
{/if}
