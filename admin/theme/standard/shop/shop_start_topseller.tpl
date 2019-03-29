{if $topseller_array}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_topseller', 'toggler', 30, '{$basepath}');

$(document).ready(function() {
    $('#container-topsellers ul').tabs();

    var options = { target: '#ajaxbasket', timeout: 3000 };
    $('.top_products').submit(function() {
        showNotice($('#top_prodmessage'), 10000);
        $(this).ajaxSubmit(options);
        return false;
    });
    $('#top_yes').on('click', function() {
        document.location = 'index.php?action=showbasket&p=shop';
        $.unblockUI();
        return false;
    });
    $('#top_no').on('click', function() {
        $.unblockUI();
        return false;
    });
});
//-->
</script>

<div id="top_prodmessage" style="display: none">
  <br />
  <p class="h3">{#Shop_ProdAddedToBasket#}</p>
  <p>{#LoginExternActions#}</p>
  <input class="shop_buttons_big" type="button" id="top_yes" value="{#Shop_go_basket#}" />
  <input class="shop_buttons_big_second" type="button" id="top_no" value="{#WinClose#}" />
  <br />
  <br />
</div>
<div class="opener">
  <div class="opened" id="navpanel_topseller" title="&lt;h3&gt;{#Shop_Topseller#}&lt;/h3&gt;">
    <div id="container-topsellers">
      <ul class="rounded">
        {assign var=count_tc value=0}
        {assign var=tc value=0}
        {foreach from=$topseller_array item=p name=topsellers}
          {assign var=count_tc value=$count_tc+1}
          {if $count_tc % $colums_topseller == 0 && !$smarty.foreach.topsellers.last || ($smarty.foreach.topsellers.first)}
            {assign var=tc value=$tc+1}
            <li><a href="#topsellers-{$tc}"><span>{#PageNavi_Page#} {$tc}</span></a></li>
            {/if}
          {/foreach}
      </ul>
      <div class="clear"></div>
      <div id="topsellers-1" class="ui-tabs-panel-content">
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            {assign var=topsellers_split value=0}
            {assign var=topseller_div_split value=0}
            {assign var=count_ts value=0}
            {foreach from=$topseller_array item=p name=topsellers}
              {assign var=topsellers_split value=$topsellers_split+1}
              {if $topsellers_split % $colums_topseller == 0 && !$smarty.foreach.topsellers.last || ($smarty.foreach.topsellers.first)}
                {assign var=topseller_div_split value=$topseller_div_split+1}
              {/if}
              {assign var=count_ts value=$count_ts+1}
              <td style="width: {$colums_width_topseller}%">
                <div class="shop_newest_boxes">
                  <div class="{if $count_ts % $colums_topseller == 0}shop_newest_first{else}shop_newest_second{/if}" {if isset($count) && $count > $newest_colums}style="border-top: 0px"{/if}>
                    <form class="top_products" method="post" action="{if empty($p.Vars) && $p.Lagerbestand>0}index.php?p=shop{else}index.php?p=shop&amp;action=showproduct&amp;id={$p.Id}{/if}">
                      <div class="shop_product_text">
                        <div class="shop_image_newstart"> <a class="stip" title="{$p.Beschreibung|tooltip:200}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}"><img class="shop_productimage_left" src="{$p.Bild_Mittel}" alt="{$p.Titel|sanitize}" /></a> </div>
                        <div class="shop_product_title_new">
                          <h2><a class="stip" title="{$p.Beschreibung|tooltip:200}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">{$p.Titel|truncate: 25|sanitize}</a></h2>
                        </div>
                      </div>
                      <div class="shop_price_detail_footer">
                        {if $shopsettings->PreiseGaeste == 1 || $loggedin}
                          {if $p.Preis > 0}
                            {if $p.Preis_Liste != $p.Preis}
                              {#Shop_instead#}&nbsp;&nbsp;<span class="shop_price_old">{$p.Preis_Liste|numformat} {$currency_symbol}</span>
                              <br/>
                            {else}
                              <br />
                            {/if}
                            {if !empty($p.Vars)}
                              {#Shop_priceFrom#}
                            {/if}
                            <span class="shop_price_start">{$p.Preis|numformat} {$currency_symbol}</span>
                            {if $no_nettodisplay != 1}
                              {if $price_onlynetto != 1}
                                <br />
                                <div class="shop_subtext">
                                  {if $shopsettings->NettoKlein == 1}
                                    {#Shop_netto#} {$p.netto_price|numformat} {$currency_symbol}
                                    <br />
                                  {/if}
                                </div>
                              {/if}
                              {if $price_onlynetto == 1 && !empty($p.price_ust_ex)}
                                <br />
                                <div class="shop_subtext">
                                  {include file="$incpath/shop/tax_inf_small.tpl"}
                                </div>
                              {/if}
                            {/if}
                          {else}
                            <br />
                            <span class="shop_price_start">{#Zvonite#}</span>
                            <br />
                            <br />
                          {/if}
                          {if $p.Fsk18 == 1 && $fsk_user != 1}
                            <br />
                            <button class="shop_buttons_big_second" type="button" onclick="location.href = '{$p.ProdLink}';"><img src="{$imgpath}/shop/basket_simple.png" alt="" /> {#buttonDetails#}</button>
                            {else}
                              {if empty($p.Vars) && $p.Lagerbestand > 0 && $p.Preis > 0 && empty($p.Frei_1) && empty($p.Frei_2) && empty($p.Frei_3)}
                              <input type="hidden" name="amount" value="1" />
                              <input type="hidden" name="action" value="to_cart" />
                              <input type="hidden" name="redir" value="{page_link}" />
                              <input type="hidden" name="product_id" value="{$p.Id}" />
                              <input type="hidden" name="ajax" value="1" />
                              <noscript>
                              <input type="hidden" name="ajax" value="0" />
                              </noscript>
                              <br />
                              <button class="shop_buttons_big" type="submit"><img src="{$imgpath}/shop/basket_simple.png" alt="" /> {#Shop_toBasket#}</button>
                              {else}
                              <input type="hidden" name="cid" value="{$p.Kategorie|default:''}" />
                              <input type="hidden" name="parent" value="{$p.Parent|default:''}" />
                              <input type="hidden" name="navop" value="{$p.Navop|default:''}" />
                              <br />
                              <button class="shop_buttons_big_second" type="button" onclick="location.href = '{$p.ProdLink}';"><img src="{$imgpath}/shop/basket_simple.png" alt="" /> {#buttonDetails#}</button>
                              {/if}
                            {/if}
                          {else}
                          <strong>{#Shop_prices_justforUsers#}</strong>
                        {/if}
                      </div>
                    </form>
                  </div>
                </div>
              </td>
              {if $count_ts % $colums_topseller == 0 && !$smarty.foreach.topsellers.last}
              </tr>
            </table>
          </div>
          <div id="topsellers-{$topseller_div_split}" class="ui-tabs-panel-content">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
              {/if}
            {/foreach}
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
{/if}
