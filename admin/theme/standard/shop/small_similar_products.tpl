{if !empty($Zub_d_products_array)}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    var options = { target: '#ajaxbasket', timeout: 3000 };
    $('.similar_products').submit(function() {
        var id = '#mylist_' + $(this).attr('id');
         alert(id );
        if ($(id).val() == 1) {
            showNotice('<br /><p class="h3">{#Shop_ProdAddedToList#}</p><br />', 2000);
        } else {
            showNotice($('#similar_prodmessage'), 10000);
        }
        $(this).ajaxSubmit(options);
        $(id).val(0);
        return false;
    });
    $('#similar_yes').on('click', function() {
        {if isset($smarty.request.blanc) && $smarty.request.blanc == 1}parent.{/if}document.location = 'index.php?action=showbasket&p=shop';
        $.unblockUI();
        return false;
    });
    $('#similar_no').on('click', function() {
        $.unblockUI();
        return false;
    });
});
//-->
</script>

<div id="similar_prodmessage" style="display: none">
  <br />
  <p class="h3">{#Shop_ProdAddedToBasket#}</p>
  <p>{#LoginExternActions#}</p>
  <input class="shop_buttons_big" type="button" id="similar_yes" value="{#Shop_go_basket#}" />
  <input class="shop_buttons_big_second" type="button" id="similar_no" value="{#WinClose#}" />
  <br />
  <br />
</div>
  <div class="shop_contents_box_tabs">
    {foreach from=$Zub_d_products_array item=p name=pro}
      {if $p.Fsk18 == 1 && $fsk_user != 1}
        <div class="error_box">{#Shop_isFSKWarning#}</div>
      {/if}
      <div class="shop_tabs_items">
        <div class="shop_tabs_items_left"> <a class="stip" title="{$p.Beschreibung|tooltip:500}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}"><img class="shop_productimage_left" src="{$p.Bild_Klein}" alt="" /></a> </div>
        <div class="shop_tabs_items_right stip" title="{$p.Beschreibung|tooltip:500}">
          <a class="shop_small_link" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">{$p.Titel|sanitize}</a>
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
        {if $shopsettings->PreiseGaeste == 1 || $loggedin}
          <div style="float: right">
            {if $p.Fsk18 == 1 && $fsk_user != 1}
              <form  method="post" action="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">
                <button class="shop_buttons_big_second" type="submit">{#buttonDetails#}</button>
              </form>
            {else}
              {if empty($p.Vars) && $p.Lagerbestand > 0 && $p.Preis > 0 && empty($p.Frei_1) && empty($p.Frei_2) && empty($p.Frei_3)}
                <form method="post" class="similar_products" id="similar_{$p.Id}" action="index.php?p=shop&amp;area={$area}">
                  <input name="amount" type="hidden" value="{if $p.MinBestellung == 0}1{else}{$p.MinBestellung}{/if}" maxlength="2" />
                  <input type="hidden" name="action" value="to_cart" />
                  <input type="hidden" name="redir" value="{page_link}#prod_anchor_{$p.Id}" />
                  <input type="hidden" name="product_id" value="{$p.Id}" />
                  <input type="hidden" name="mylist" id="mylist_similar_{$p.Id}" value="0" />
                  <input type="hidden" name="ajax" value="1" />
                  <noscript>
                  <input type="hidden" name="ajax" value="0" />
                  </noscript>
                  <button class="shop_buttons_big" type="submit">{#Shop_toBasket#}</button>&nbsp;
                  <button class="shop_buttons_big_second" onclick="document.getElementById('mylist_similar_{$p.Id}').value = '1';" type="submit">{#Shop_WishList#}</button>
                </form>
              {else}
                <form method="post" action="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">
                  <button class="shop_buttons_big" type="submit">{#buttonDetails#}</button>
                </form>
              {/if}
            {/if}
          </div>
        {else}
          <strong>{#Shop_prices_justforUsers#}</strong>
        {/if}
        <div class="clear">&nbsp;</div>
      </div>
    {/foreach}
  </div>
{/if}
