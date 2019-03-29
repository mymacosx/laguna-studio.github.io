{if $extern_products_array}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#container-options ul.rounded').tabs();
});
togglePanel('navpanel_shopnewin', 'toggler', 30, '{$basepath}');
//-->
</script>

<div class="opener">
  <div class="opened" id="navpanel_shopnewin" title="&lt;h3&gt;{#Shop_NewProducts#}&lt;/h3&gt;">
    <div id="container-options">
      <ul class="rounded">
        {assign var=count2 value=0}
        {assign var=nc value=0}
        {foreach from=$extern_products_array item=p name=pro}
          {assign var=count2 value=$count2+1}
          {if $count2 % $newest_colums == 0 && !$smarty.foreach.pro.last || ($smarty.foreach.pro.first)}
            {assign var=nc value=$nc+1}
            <li><a href="#opt-{$nc}"><span>{#PageNavi_Page#} {$nc}</span></a></li>
            {/if}
          {/foreach}
      </ul>
      <div class="clear"></div>
      <div id="opt-1" class="ui-tabs-panel-content">
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            {assign var=count value=0}
            {assign var=div_split value=0}
            {assign var=count_split value=0}
            {foreach from=$extern_products_array item=p name=pro}
              {assign var=count_split value=$count_split+1}
              {if $count_split % $newest_colums == 0 && !$smarty.foreach.pro.last || ($smarty.foreach.pro.first)}
                {assign var=div_split value=$div_split+1}
              {/if}
              {assign var=count value=$count+1}
              <td style="width: {$colums_width}%">
                <div class="shop_newest_boxes">
                  <div class="{if $count % $newest_colums == 0}shop_newest_first{else}shop_newest_second{/if}" {if $count > $newest_colums}style="border-top: 0px"{/if}>
                    <div class="shop_product_text">
                      <div class="shop_image_newstart"> <a title="{$p.Titel|sanitize} - {$p.Beschreibung|striptags|truncate: 150|sanitize}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}"><img src="{$p.Bild_Mittel}" alt="{$p.Titel|sanitize}" border="0" /></a> </div>
                      <div class="shop_product_title_new"> <a title="{$p.Titel|sanitize} - {$p.Beschreibung|striptags|truncate: 150|sanitize}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">{$p.Titel|truncate: 25|sanitize}</a> </div>
                    </div>
                    <div class="shop_price_detail_footer">
                      {#Arrow#}<a title="{$p.Titel|sanitize}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">{#MoreDetails#}</a>
                      <br />
                      <br />
                      {if $shopsettings->PreiseGaeste == 1 || $loggedin}
                        {if $p.Preis_Liste != $p.Preis}
                          {#Shop_instead#}&nbsp;&nbsp;
                          <br/>
                          <span class="shop_price_old">{$p.Preis_Liste|numformat} {$currency_symbol}</span>
                          <br/>
                        {else}
                          <br />
                        {/if}
                        {if !empty($p.Vars)}
                          {#Shop_priceFrom#}
                        {/if}
                        {if $p.Preis > 0}
                          <span class="shop_price">{$p.Preis|numformat} {$currency_symbol}</span>
                          {if $no_nettodisplay != 1}
                            {if $price_onlynetto != 1}
                              <br />
                              <div class="shop_subtext">
                                {include file="$incpath/shop/tax_inf_small.tpl"}
                                <br />
                                {if $shopsettings->NettoKlein == 1}
                                  {#Shop_netto#} {$p.netto_price|numformat} {$currency_symbol}
                                {/if}
                              </div>
                            {/if}
                            {if $price_onlynetto == 1 && !empty($p.price_ust_ex)}
                              <br />
                              <div class="shop_subtext"> {include file="$incpath/shop/tax_inf_small.tpl"} </div>
                            {/if}
                          {/if}
                        {else}
                          <span class="shop_price">{#Zvonite#}</span>
                          <br />
                          <div class="shop_subtext">
                            <br />
                            <br />
                            <br />
                          </div>
                        {/if}
                      {else}
                        <strong>{#Shop_prices_justforUsers#}</strong>
                      {/if}
                    </div>
                  </div>
                </div>
              </td>
              {if $count % $newest_colums == 0 && !$smarty.foreach.pro.last}
              </tr>
            </table>
          </div>
          <div id="opt-{$div_split}">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
              {else}
              {/if}
            {/foreach}
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
{/if}
