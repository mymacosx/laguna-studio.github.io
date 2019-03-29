<div class="box_innerhead">
  <div style="float: left">
    <div class="h4">{#Shop_savedBasketLink1#}</div>
  </div>
  <div style="float: right">
    <form method="post" action="index.php?p=shop" onsubmit="return confirm('{#Shop_basket_delallC#}');">
      <input type="hidden" name="action" value="delsavedbasket_all" />
      <input type="hidden" name="bid" value="{$sb->Id}" />
      <input type="submit" class="button" value="{#Shop_basket_delall#}" />
    </form>
  </div>
  <div class="clear"></div>
</div>
{if $saved_found}
  {foreach from=$saved item=sb}
    {assign var=itera value=$sb->Id}
    <div class="infobox">
      <div class="h4">{#Shop_savedBasketFrom#} {$sb->ZeitBis|date_format: "%d.%m.%y, %H:%M"}</div>
      <br />
      <table width="100%" cellspacing="0" cellpadding="5">
        <tr>
          <td><strong>{#GlobalImage#}</strong></td>
          <td><strong>{#Shop_f_artNr#}</strong></td>
          <td><strong>{#Shop_variants_d#}</strong></td>
          <td><strong>{#Konfiguration#}</strong></td>
          <td><strong>{#Shop_amount#}</strong></td>
        </tr>
        {foreach from=$sb->Positions item=p name=posit}
          {if $smarty.foreach.posit.first}
            {assign var=iter value='shop_basket_second,shop_basket_first'}
          {/if}
          <tr>
            <td width="75" valign="top" class="{cycle name="d1$itera" values=$iter}"><a href="{$p->ProdLink}"><img src="{$p->Bild}" border="0" alt="" /></a></td>
            <td valign="top" class="{cycle name="d2$itera" values=$iter}">
              <a class="shop_small_link" href="{$p->ProdLink}">{$p->Titel|sanitize}</a>
              <br />
              {$p->Artikelnummer}
            </td>
            <td valign="top" class="{cycle name="d3$itera" values=$iter}">
              {if $p->Vars}
                {foreach from=$p->Vars item=v}
                  <strong>{$v->KatName}</strong>: {$v->Name} ({$v->Wert|numformat} {$currency_symbol})
                  <br />
                {/foreach}
              {else}
                -
              {/if}
            </td>
            <td valign="top" class="{cycle name="d4$itera" values=$iter}"> {$p->FreeFields|default:'-'} </td>
            <td valign="top" class="{cycle name="d5$itera" values=$iter}"> {$p->Anzahl} </td>
          </tr>
        {/foreach}
      </table>
      <div style="text-align: right; margin-top: 10px">
        <form method="post" action="index.php?p=shop"{if $basket_products_price > 0 || $basket_products_all >=1} onsubmit="return confirm('{#Shop_savedBasketLoadC#}');"{/if}>
          <input type="hidden" name="action" value="loadsavedbasket" />
          <input type="hidden" name="bid" value="{$sb->Id}" />
          <input type="submit" class="button" value="{#Shop_savedBasketLoad#}" />
        </form>
        <form method="post" action="index.php?p=shop" onsubmit="return confirm('{#Shop_savedBasketDel#}');">
          <input type="hidden" name="action" value="delsavedbasket" />
          <input type="hidden" name="bid" value="{$sb->Id}" />
          <input type="submit" class="button" value="{#Delete#}" />
        </form>
      </div>
    </div>
  {/foreach}
{else}
  <div class="h3">{#Shop_savedBasketNo#}</div>
{/if}
