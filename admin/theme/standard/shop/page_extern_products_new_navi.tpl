{if $extern_products_array}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_shopnew', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_shopnew" title="{#Shop_newin#}">
    <div class="boxes_body">
      {foreach from=$extern_products_array item=p name=pro}
        <div class="shop_extern_newest_boxes">
          <div class="shop_extern_product_text stip" title="{$p.Beschreibung|tooltip}">
            <div class="shop_extern_image_newstart"><a href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}"><img src="{$p.Bild_Mittel}" alt="" /></a></div>
            <br />
            <div class="shop_product_title_new"><a href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">{$p.Titel|truncate: 25|sanitize}</a></div>
          </div>
          <div class="shop_price_detail_footer">
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
                      {if $shopsettings->NettoKlein == 1}
                        {#Shop_netto#} {$p.netto_price|numformat} {$currency_symbol}
                        <br />
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
              {/if}
            {else}
              <strong>{#Shop_prices_justforUsers#}</strong>
            {/if}
          </div>
        </div>
      {/foreach}
    </div>
  </div>
</div>
{/if}
