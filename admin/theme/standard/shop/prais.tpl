<div class="box_innerhead">{#Shop_Preis#}</div>
<table class="box_inner" width="100%" cellspacing="1" cellpadding="0">
  <tr>
    <td width="10%" align="center"><strong>{#Shop_ArticleNumber#}</strong></td>
    <td width="80%" align="center"><strong>{#GlobalTitle#}</strong></td>
    <td width="10%" align="center"><strong>{#Products_price#}</strong></td>
  </tr>
  {foreach from=$prais key=categ item=products}
    <tr>
      <td colspan="3" class="shop_prais_categ">{$categ}</td>
    </tr>
    {foreach from=$products item=tovar}
    <tr>
      <td align="center" valign="middle" class="shop_prais_product">{$tovar->Artikelnummer|sanitize}</td>
      <td valign="middle" class="shop_prais_product"><a href="index.php?p=shop&amp;action=showproduct&amp;id={$tovar->Id}&amp;cid={$tovar->Kategorie}&amp;pname={$tovar->Titel|translit}">{$tovar->Titel|sanitize}</a><br /><small>{$tovar->Beschreibung|striptags|truncate: 200|sanitize}</small></td>
      <td align="center" valign="middle" nowrap="nowrap" class="shop_prais_product">
        {if $shopsettings->PreiseGaeste == 1 || $loggedin}
          {if $tovar->Preis_Liste > 0}
            {$tovar->Preis_Liste|numformat} {$currency_symbol}
          {else}
            {#Zvonite#}
          {/if}
        {else}
          <strong>{#Shop_prices_justforUsers#}</strong>
        {/if}
      </td>
    </tr>
    {/foreach}
  {/foreach}
</table>
<br />
{if !empty($pages)}
  {$pages}
{/if}
