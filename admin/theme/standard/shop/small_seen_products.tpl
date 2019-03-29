<script type="text/javascript">
<!-- //
$(document).ready(function() {
    var options = { target: '#ajaxbasket', timeout: 3000 };
    $('.seen_products').submit(function() {
        var id = '#mylist_' + $(this).attr('id');
        if ($(id).val() == 1) {
            showNotice('<br /><p class="h3">{#Shop_ProdAddedToList#}</p><br />', 2000);
        } else {
            showNotice($('#seen_prodmessage'), 10000);
        }
        $(this).ajaxSubmit(options);
        $(id).val(0);
        return false;
    });
    $('#seen_yes').on('click', function() {
        document.location = 'index.php?action=showbasket&p=shop';
        $.unblockUI();
        return false;
    });
    $('#seen_no').on('click', function() {
        $.unblockUI();
        return false;
    });
});
//-->
</script>

<div id="seen_prodmessage" style="display: none">
  <br />
  <p class="h3">{#Shop_ProdAddedToBasket#}</p>
  <p>{#LoginExternActions#}</p>
  <input class="shop_buttons_big" type="button" id="seen_yes" value="{#Shop_go_basket#}" />
  <input class="shop_buttons_big_second" type="button" id="seen_no" value="{#WinClose#}" />
  <br />
  <br />
</div>
{if $smarty.request.action != 'showproduct'}
  <br />
  <div class="shop_headers">{#Shop_detailLastSeen#}</div>
{/if}
<div class="shop_contents_box_tabs">
  {foreach from=$seen_products_array item=p name=pro}
    {if $p.Fsk18 == 1 && $fsk_user != 1}
      <div class="error_box">{#Shop_isFSKWarning#}</div>
    {/if}
    <div class="shop_tabs_items">
      <div class="shop_tabs_items_left"><a class="stip" title="{$p.Beschreibung|tooltip:500}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}"><img class="shop_productimage_left" src="{$p.Bild_Klein}" alt="" /></a></div>
      <div class="shop_tabs_items_right stip" title="{$p.Beschreibung|tooltip:500}">
        <h4><a class="shop_small_link" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">{$p.Titel|sanitize}</a></h4>
        <br />
        {if $shopsettings->PreiseGaeste == 1 || $loggedin}
          {if $p.Preis > 0}
            <strong>{$p.Preis|numformat} {$currency_symbol}</strong><span class="sup">*</span>
          {else}
            <strong>{#Zvonite#}</strong>
          {/if}
        {else}
          <strong>{#Shop_prices_justforUsers#}</strong>
        {/if}
      </div>
      <div style="float: right">
        {if $p.Fsk18 == 1 && $fsk_user != 1}
          <form method="post" action="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">
            <button class="shop_buttons_big_second" type="submit">{#buttonDetails#}</button>
          </form>
        {else}
          {if empty($p.Vars) && $p.Lagerbestand > 0 && $p.Preis > 0 && empty($p.Frei_1) && empty($p.Frei_2) && empty($p.Frei_3)}
            <form method="post" class="seen_products" id="seen_{$p.Id}" name="products_{$p.Id}" action="{if empty($p.Vars)}index.php?p=shop{else}index.php?p=shop&amp;action=showproduct&amp;id={$p.Id}{/if}">
              <input name="amount" type="hidden" value="{if $p.MinBestellung == 0}1{else}{$p.MinBestellung}{/if}" maxlength="2" />
              <input type="hidden" name="action" value="to_cart" />
              <input type="hidden" name="redir" value="{page_link}#prod_anchor_{$p.Id}" />
              <input type="hidden" name="product_id" value="{$p.Id}" />
              <input type="hidden" name="mylist" id="mylist_seen_{$p.Id}" value="0" />
              <input type="hidden" name="ajax" value="1" />
              <noscript>
              <input type="hidden" name="ajax" value="0" />
              </noscript>
              <button class="shop_buttons_big" type="submit">{#Shop_toBasket#}</button>&nbsp;
              <button class="shop_buttons_big_second" onclick="document.getElementById('mylist_seen_{$p.Id}').value = '1';" type="submit">{#Shop_WishList#}</button>
            </form>
          {else}
            <form method="post" action="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">
              <button class="shop_buttons_big" type="submit">{#buttonDetails#}</button>
            </form>
          {/if}
        {/if}
      </div>
      <div class="clear">&nbsp;</div>
    </div>
  {/foreach}
  <br />
  {#Arrow#}<a href="index.php?{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}blanc=1&amp;{/if}p=shop&amp;area={$area}&amp;action=showseenproducts">{#Shop_showSeenProducts#}</a>
</div>
