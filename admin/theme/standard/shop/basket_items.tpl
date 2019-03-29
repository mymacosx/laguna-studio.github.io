<table width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td colspan="2" class="shop_basket_header">{#Products#}</td>
    <td class="shop_basket_header">{#Shop_amount#}</td>
    <td align="right" class="shop_basket_header">{#Products_price#}</td>
    <td align="right" class="shop_basket_header">{#Shop_basket_ovall#}</td>
  </tr>
  {foreach from=$product_array item=p}
  <tr>
    <td class="{cycle name='d' values='shop_basket_first,shop_basket_second'}"><a href="{$p->ProdLink}"><img src="{$p->Bild}" alt="" /></a></td>
    <td class="{cycle name='d2' values='shop_basket_first,shop_basket_second'}">
      <strong><a href="{$p->ProdLink}">{$p->Titel|sanitize}</a></strong>
      <br />
      <small>{#Shop_f_artNr#}: {$p->Artikelnummer}</small>
      <br />
      {foreach from=$p->Vars item=v}
      <small><strong>{$v->KatName}</strong>: {$v->Name} ({$v->Wert|numformat} {$currency_symbol})</small>
      <br />
      {/foreach}
      {if $p->FreeFields}
      <br />
      <small><strong>{#Konfiguration#}</strong></small>
      <br />
      <small>{$p->FreeFields}</small> {/if} <strong>{$lang.Shop_shipping_timeinf|replace: ': ': ''}</strong>
      <br />
      <small>{$p->Lieferzeit}</small>
    </td>
    <td nowrap="nowrap" class="{cycle name='d3' values='shop_basket_first,shop_basket_second'}">
      {if $basket_display == 'step4'}
      {$p->Anzahl}
      {else}
      <form method="post" action="index.php">
        <input class="input" name="amount" type="text" style="width: 30px" value="{$p->Anzahl}" maxlength="5" />
        {foreach from=$p->Varianten item=x}
        <input type="hidden" name="mod[]" value="{$x}" />
        {/foreach}
        <input type="hidden" name="basket_refresh" value="1" />
        <input type="hidden" name="p" value="shop" />
        <input type="hidden" name="area" value="{$area}" />
        <input type="hidden" name="action" value="to_cart" />
        <input type="hidden" name="redir" value="{page_link}" />
        <input type="hidden" name="product_id" value="{$p->Id}" />
        <input class="absmiddle stip" title="{$lang.Shop_refreshItem|tooltip}" type="image" src="{$imgpath}/shop/refresh.png" />
      </form>
      <form method="post" action="index.php">
        {foreach from=$p->Varianten item=x}
        <input type="hidden" name="mod[]" value="{$x}" />
        {/foreach}
        <input type="hidden" name="p" value="shop" />
        <input type="hidden" name="area" value="{$area}" />
        <input type="hidden" name="action" value="delitem" />
        <input type="hidden" name="redir" value="{page_link}" />
        <input type="hidden" name="product_id" value="{$p->Id}" />
        <input class="absmiddle stip" title="{$lang.Shop_delItem|tooltip}" type="image" src="{$imgpath}/shop/delete.png" />
      </form>
      {/if}
    </td>
    <td align="right" nowrap="nowrap" class="{cycle name='d4' values='shop_basket_first,shop_basket_second'}">
      {if $show_vat_table == 1 && $shopsettings->NettoPreise == 1}
      {if $show_vat_table == 1}
      {#Shop_netto#}
      {/if}
      <strong>{$p->Preis|numformat} {$currency_symbol}</strong>
      {if $show_vat_table == 1  && $shopsettings->NettoPreise != 1}
      <br />
      <small>{#Shop_brutto#} {$p->Preis_b|numformat} {$currency_symbol}</small>
      {/if}
      {else}
      {if $show_vat_table != 1}
      <strong>{$p->Preis|numformat} {$currency_symbol}</strong>
      {else}
      <strong>{$p->Preis_b|numformat} {$currency_symbol}</strong>
      {if $show_vat_table == 1}
      <br />
      <small>{#Shop_netto#} {$p->Preis|numformat} {$currency_symbol}</small>
      {/if}
      {/if}
      {/if}
    </td>
    <td align="right" nowrap="nowrap" class="{cycle name='d5' values='shop_basket_first,shop_basket_second'}">
      {if $show_vat_table == 1 && $shopsettings->NettoPreise == 1}
      {if $show_vat_table == 1}
      {#Shop_netto#}
      {/if}
      <strong>{$p->Endpreis|numformat} {$currency_symbol}</strong>
      {if $show_vat_table == 1  && $shopsettings->NettoPreise != 1}
      <br />
      <small>{#Shop_brutto#} {$p->Preis_bs|numformat} {$currency_symbol}</small>
      {/if}
      {else}
      {if $show_vat_table != 1}
      <strong>{$p->Endpreis|numformat} {$currency_symbol}</strong>
      {else}
      <strong>{$p->Preis_bs|numformat} {$currency_symbol}</strong>
      {if $show_vat_table == 1}
      <br />
      <small>{#Shop_netto#} {$p->Endpreis|numformat} {$currency_symbol}</small>
      {/if}
      {/if}
      {/if}
    </td>
  </tr>
  {/foreach}
</table>
