<script type="text/javascript">
<!-- //
$(document).ready(function() {
    var options = { target: '#ajaxbasket', timeout: 3000 };
    $('.ajax_products').submit(function() {
        var id = '#mylist_' + $(this).attr('id');
        if ($(id).val() == 1) {
            showNotice('<br /><p class="h3">{#Shop_ProdAddedToList#}</p><br />', 2000);
        } else {
            showNotice($('#ajax_prodmessage'), 10000);
        }
        $(this).ajaxSubmit(options);
        $(id).val(0);
        return false;
    });
    $('#ajax_yes').on('click', function() {
        document.location = 'index.php?action=showbasket&p=shop';
        $.unblockUI();
        return false;
    });
    $('#ajax_no').on('click', function() {
        $.unblockUI();
        return false;
    });
});
//-->
</script>

<div id="ajax_prodmessage" style="display: none">
  <br />
  <p class="h3">{#Shop_ProdAddedToBasket#}</p>
  <p>{#LoginExternActions#}</p>
  <input class="shop_buttons_big" type="button" id="ajax_yes" value="{#Shop_go_basket#}" />
  <input class="shop_buttons_big_second" type="button" id="ajax_no" value="{#WinClose#}" />
  <br />
  <br />
</div>
{if $shopsettings->TopNewOffersPos == 'top' && $printversion != 1}
{include file="$incpath/shop/categ_tabs.tpl"}
{/if}
{if $smarty.request.s == 1}
{include file="$incpath/shop/search_extended.tpl"}
<br />
{/if}
{if $cat_desc}
<div class="shop_cat_desc">{$cat_desc}</div>
{/if}
<div class="shop_headers">{#Shop_productOverview#}</div>
{include file="$incpath/shop/products_headernavi.tpl" position="top"}
{if !$products}
<div class="shop_empty_categ">
  {if !empty($smarty.request.shop_q)}
  {#Shop_searchNull#}
  {else}
  {#Shop_noProducts#}
  {/if}
</div>
<br />
{else}
{foreach from=$products item=p name=pro}
{assign var=count value=$count+1}
<a name="prod_anchor_{$p.Id}"></a>
<div class="shop_products_list">
  <form method="post" name="products_{$p.Id}" id="ajax_{$p.Id}" class="ajax_products" action="{if empty($p.Vars)}index.php?p=shop&amp;area={$area}{else}index.php?p=shop&amp;area={$area}&amp;action=showproduct&amp;id={$p.Id}{/if}">
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="150" valign="top">
          {if $shopsettings->popup_product == 1}
          <a class="colorbox" title="{$p.Titel|sanitize}" href="{$p.ProdLink}&amp;blanc=1"><img class="shop_productimage_list" src="{$p.Bild_Mittel}" alt="{$p.Titel|sanitize}" /></a>
          {else}
          <a title="{$p.Titel|sanitize} - {$p.Beschreibung|striptags|truncate: 500|sanitize}" href="{$p.ProdLink}"><img class="shop_productimage_list" src="{$p.Bild_Mittel}" alt="{$p.Titel|sanitize}" /></a>
          {/if}
          <br />
          {if $p.Fsk18 == 1}
          <p align="center">
            <img src="{$imgpath_page}usk_small.gif" alt="{#Shop_isFSKWarning#}" />
            <br />
            <strong>{#Shop_isFSKWarning#}</strong>
          </p>
          {/if}
        </td>
        <td valign="top">
          {if $shopsettings->popup_product == 1}
          <h3><a class="colorbox stip" title="{$p.Beschreibung|tooltip:500}" href="{$p.ProdLink}&amp;blanc=1">{$p.Titel|sanitize}</a></h3>
          {else}
          <h3><a class="stip" title="{$p.Beschreibung|tooltip:500}" href="{$p.ProdLink}">{$p.Titel|sanitize}</a></h3>
          {/if}
          {if $shopsettings->Zeige_Text == 1 && !empty($p.Beschreibung)}
          {$p.Beschreibung|striptags|truncate: $shopsettings->Prodtext_Laenge|sanitize}
          <br />
          <br />
          {/if}
          {if $shopsettings->Zeige_Verfuegbarkeit == 1 || $shopsettings->Zeige_Lagerbestand == 1 || $shopsettings->Zeige_Lieferzeit == 1 ||
          $shopsettings->Zeige_ArtNr == 1 || $shopsettings->Zeige_Hersteller == 1 || $shopsettings->Zeige_ErschienAm == 1}
          <table cellpadding="0" cellspacing="0">
            {if $shopsettings->Zeige_Verfuegbarkeit == 1}
            <tr>
              <td width="150">{#Shop_Availablility#}: </td>
              <td align="right">{$p.VIcon}</td>
            </tr>
            {/if}
            {if $p.Lieferzeit && $p.Lagerbestand>0}
            {if $shopsettings->Zeige_Lagerbestand == 1}
            <tr>
              <td width="150">{#Shop_av_store#}: </td>
              <td align="right">{#Shop_av_storeAv#} {$p.Lagerbestand}</td>
            </tr>
            {/if}
            {if $shopsettings->Zeige_Lieferzeit == 1}
            <tr>
              <td width="150">{#Shop_shipping_timeinf#}</td>
              <td align="right">{$p.Lieferzeit|sanitize}</td>
            </tr>
            {/if}
            {/if}
            {if $shopsettings->Zeige_ArtNr == 1}
            <tr>
              <td width="150">{#Shop_ArticleNumber#}: </td>
              <td align="right"><strong>{$p.Artikelnummer}</strong></td>
            </tr>
            {/if}
            {if $p.man->Id >= 1 && $shopsettings->Zeige_Hersteller == 1}
            <tr>
              <td width="150">{#Manufacturer#}: </td>
              <td align="right"><a href="index.php?p=shop&amp;action=showproducts&amp;man={$p.man->Id}">{$p.man->Name}</a></td>
            </tr>
            {/if}
            {if $shopsettings->Zeige_ErschienAm == 1}
            <tr>
              <td width="150">{#Added#}</td>
              <td align="right">{$p.Erstellt|date_format: $lang.DateFormatSimple}</td>
            </tr>
            {/if}
          </table>
          <br />
          {/if}
          <div class="shop_products_list_bboxheader" style="margin-top: 5px">
            <div style="float: left">
              {if $shopsettings->PreiseGaeste == 1 || $loggedin}
              {if $p.Preis_Liste != $p.Preis}
              {#Shop_instead#}&nbsp;<span class="shop_price_old">{$p.Preis_Liste|numformat} {$currency_symbol}</span>
              <br />
              {/if}
              {if !empty($p.Vars)}
              {#Shop_priceFrom#}
              {/if}
              {if $p.Preis > 0}
              <span class="shop_price">{$p.Preis|numformat} {$currency_symbol}</span>
              {else}
              <span class="shop_price">{#Zvonite#}</span>
              {/if}
              {else}
              <strong>{#Shop_prices_justforUsers#}</strong>
              {/if}
            </div>
            <div style="float: right">
              {if empty($p.Vars) && $p.Lagerbestand > 0 && $p.Preis > 0 && empty($p.Frei_1) && empty($p.Frei_2) && empty($p.Frei_3)}
              {if $p.Fsk18 == 1 && $fsk_user != 1}
              {else}
              {#Shop_amount#}&nbsp;
              <input class="input" name="amount" type="text" style="width: 40px" value="1" maxlength="3" />
              {/if}
              {/if}
            </div>
            <div class="clear"></div>
          </div>
          <div class="shop_products_list_bbox">
            <div class="shop_products_list_bcontent">
              {if $p.shipping_free == 1}
              <div class="product_important_noshipping small">{#Shop_freeshipping#}</div>
              {/if}
              {if $p.diffpro > 0}
              <div class="product_important_cheaper small">{#Shop_Billiger#}{$p.diffpro|numformat}%</div>
              {/if}
              {if $shopsettings->PreiseGaeste == 1 || $loggedin}
              <div style="float: right">
                {if $p.Fsk18 == 1 && $fsk_user != 1}
                {if $shopsettings->popup_product == 1}
                <button class="shop_buttons_big_second" type="button" onclick="newWindow('{$p.ProdLink}&amp;blanc=1', '90%', '97%');"><img src="{$imgpath}/shop/basket_simple.png" alt="" /> {#buttonDetails#}</button>
                {else}
                <button class="shop_buttons_big_second" type="button" onclick="location.href='{$p.ProdLink}';"><img src="{$imgpath}/shop/basket_simple.png" alt="" /> {#buttonDetails#}</button>
                {/if}
                {else}
                {if empty($p.Vars) && $p.Lagerbestand > 0 && $p.Preis > 0 && empty($p.Frei_1) && empty($p.Frei_2) && empty($p.Frei_3)}
                <input type="hidden" name="action" value="to_cart" />
                <input type="hidden" name="redir" value="{page_link}{*#prod_anchor_{$p.Id}*}" />
                <input type="hidden" name="product_id" value="{$p.Id}" />
                <input type="hidden" name="mylist" id="mylist_ajax_{$p.Id}" value="0" />
                <input type="hidden" name="ajax" value="1" />
                <noscript>
                <input type="hidden" name="ajax" value="0" />
                </noscript>
                <button class="shop_buttons_big" type="submit"><img src="{$imgpath}/shop/basket_simple.png" alt="" /> {#Shop_toBasket#}</button>
                <button class="shop_buttons_big_second" onclick="document.getElementById('mylist_ajax_{$p.Id}').value='1';" type="submit"><img src="{$imgpath}/shop/wishlist.png" alt="" />{#Shop_WishList#}</button>
                {else}
                <input type="hidden" name="parent" value="{$p.Parent}" />
                <input type="hidden" name="navop" value="{$p.Navop}" />
                <input type="hidden" name="cid" value="{$p.Kategorie}" />
                {if $shopsettings->popup_product == 1}
                <button class="shop_buttons_big" type="button" onclick="newWindow('{$p.ProdLink}&amp;blanc=1', '90%', '97%');"><img src="{$imgpath}/shop/basket_simple.png" alt="" /> {#buttonDetails#}</button>
                {else}
                <button class="shop_buttons_big" type="button" onclick="location.href='{$p.ProdLink}';"><img src="{$imgpath}/shop/basket_simple.png" alt="" /> {#buttonDetails#}</button>
                {/if}
                {/if}
                {/if}
              </div>
              {/if}
              <div class="clear"></div>
            </div>
          </div>
        </td>
      </tr>
    </table>
  </form>
</div>
{/foreach}
{include file="$incpath/shop/products_headernavi.tpl" position="bottom"}
{if $shopsettings->TopNewOffersPos == 'bottom'}
{include file="$incpath/shop/categ_tabs.tpl"}
{/if}
{/if}
{if $smarty.request.s != '1'}
{include file="$incpath/shop/products_navi_bottom.tpl"}
{/if}
{if $shopsettings->seen_cat == 1}
  {$small_seen_products}
{/if}
{if $shopsettings->vat_info_cat == 1}
{include file="$incpath/shop/vat_info.tpl"}
{/if}
