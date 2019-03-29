<div class="box_innerhead">{#Shop_mylist#}</div>
{#Shop_mylist_inf#}
{if !$product_array}
  <div class="error_box"> {#Shop_mylist_false#} </div>
{else}
  <br />
  <br />
  <table width="100%" cellspacing="1" cellpadding="2">
    <tr>
      <td colspan="2" class="shop_basket_header">{#Products#}</td>
      <td align="center" class="shop_basket_header" nowrap="nowrap">{#Shop_basket_amount2#}</td>
      <td align="right" class="shop_basket_header">{#Products_price#}</td>
      <td align="right" class="shop_basket_header">{#Shop_basket_ovall#}</td>
      <td align="center" class="shop_basket_header">&nbsp;</td>
    </tr>
    {foreach from=$product_array item=p}
      <tr class="{cycle name='d' values='row_first,row_second'}">
        <td><a href="{$p->ProdLink}"><img src="{$p->Bild}" alt="" /></a></td>
        <td>
          <a class="shop_small_link" href="{$p->ProdLink}">{$p->Titel|sanitize}</a>
          <br />
          <small>{#Shop_f_artNr#}: {$p->Artikelnummer}</small>
          <br />
          {foreach from=$p->Vars item=v}
            <small><strong>{$v->KatName}</strong>: {$v->Name} ({$v->Wert|numformat} {$currency_symbol})</small>
            <br />
          {/foreach}
        </td>
        <td nowrap="nowrap" align="center">{$p->Anzahl}</td>
        <td align="right" nowrap="nowrap">
          {if $shopsettings->PreiseGaeste == 1 || $loggedin}
            {if $p->Preis > 0}
              {#Shop_netto#} <strong>{$p->Preis|numformat} {$currency_symbol}</strong>
              {if $show_vat_table == 1}
                <br />
                <small>{#Shop_brutto#} {$p->Preis_b|numformat} {$currency_symbol}</small>
              {/if}
            {else}
              <strong>{#Zvonite#}</strong>
            {/if}
          {else}
            <strong>{#Shop_prices_justforUsers#}</strong>
          {/if}
        </td>
        <td align="right" nowrap="nowrap">
          {if $shopsettings->PreiseGaeste == 1 || $loggedin}
            {if $p->Endpreis > 0}
              {#Shop_netto#} <strong>{$p->Endpreis|numformat} {$currency_symbol}</strong>
              {if $show_vat_table == 1}
                <br />
                <small>{#Shop_brutto#} {$p->Preis_bs|numformat} {$currency_symbol}</small>
              {/if}
            {else}
              <strong>{#Zvonite#}</strong>
            {/if}
          {else}
            <strong>{#Shop_prices_justforUsers#}</strong>
          {/if}
        </td>
        <td style="width: 100px">
          <form name="to_basket_{$p->Id}" method="post" action="index.php">
            <input name="amount" type="hidden" value="{$p->Anzahl}" />
            {foreach from=$p->Varianten item=x}
              <input type="hidden" name="mod[]" value="{$x}" />
            {/foreach}
            <input type="hidden" name="p" value="shop" />
            <input type="hidden" name="area" value="{$area}" />
            <input type="hidden" name="action" value="to_cart" />
            <input type="hidden" name="redir" value="{page_link}" />
            <input type="hidden" name="product_id" value="{$p->Id}" />
            {if $p->Endpreis > 0}
              {#Arrow#}<a href="javascript: document.forms['to_basket_{$p->Id}'].submit();">{#Shop_toBasket#}</a>
              <br />
            {/if}
          </form>
          <form name="delete_item_{$p->Id}" method="post" action="index.php">
            <input name="amount" type="hidden" value="{$p->Anzahl}" />
            {foreach from=$p->Varianten item=x}
              <input type="hidden" name="mod[]" value="{$x}" />
            {/foreach}
            <input type="hidden" name="p" value="shop" />
            <input type="hidden" name="area" value="{$area}" />
            <input type="hidden" name="action" value="delitem_mylist" />
            <input type="hidden" name="redir" value="{page_link}" />
            <input type="hidden" name="product_id" value="{$p->Id}" />
            {#Arrow#}<a href="javascript: document.forms['delete_item_{$p->Id}'].submit();">{#Delete#}</a>
          </form>
        </td>
      </tr>
    {/foreach}
  </table>
  <br />
  <div class="clear"></div>
  <form method="post" action="index.php">
    <input type="hidden" name="p" value="shop" />
    <input type="hidden" name="area" value="{$area}" />
    <input type="hidden" name="action" value="new_list" />
    <input type="submit" class="button" value="{#Shop_mylist_new#}" />
  </form>
  <br />
  <br />
  {if !$loggedin}
    {#Shop_mylist_save_notlogged#}
  {else}
    <form method="post" action="index.php">
      <input type="hidden" name="p" value="shop" />
      <input type="hidden" name="area" value="{$area}" />
      <input type="hidden" name="action" value="mylist" />
      <input type="hidden" name="subaction" value="save_list" />
      <input type="text" class="input" value="{#GlobalTitle#}" name="Name_Merkzettel" />&nbsp;
      <input type="submit" class="button" value="{#Shop_mylist_save#}" />
    </form>
  {/if}
{/if}
<br />
<br />
<div class="shop_headers">{#Shop_mylist_load#}</div>
{if !$loggedin}
  {#Shop_mylist_load_notlogged#}
{else}
  {if $myLists}
    <table width="100%" cellpadding="2" cellspacing="0">
      <tr>
        <td><strong>{#Shop_mylist_list#}</strong></td>
        <td><strong>{#Date#}</strong></td>
        <td align="right">&nbsp;</td>
      </tr>
      {foreach from=$myLists item=ml}
        <tr class="{cycle name='mylists' values='row_first,row_second'}">
          <td width="33%"><a href="index.php?p=shop&amp;action=mylist&amp;subaction=load_list&amp;id={$ml->Id}">{$ml->Name|sanitize}</a></td>
          <td width="33%">{$ml->Datum|date_format: $lang.DateFormat}</td>
          <td width="33%" align="right">{#Arrow#}<a href="index.php?p=shop&amp;action=mylist&amp;subaction=del_list&amp;id={$ml->Id}">{#Shop_mylist_dellist#}</a></td>
        </tr>
      {/foreach}
    </table>
  {else}
    {#Shop_mylist_nothing#}
  {/if}
{/if}
