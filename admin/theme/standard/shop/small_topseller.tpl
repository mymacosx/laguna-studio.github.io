{if $small_topseller_array}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_topsellers', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

<div class="round">
  <div class="opened" id="navpanel_topsellers" title="{#Shop_Topseller#}">
    <div class="boxes_body">
      {foreach from=$small_topseller_array item=p name=pro}
        <div class="shop_products_small">
          <div class="shop_products_list_left"> <a class="stip" title="{$p.Beschreibung|tooltip:500}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}"><img class="shop_productimage_left" src="{$p.Bild_Klein}" alt="" /></a></div>
          <div class="shop_products_list_right">
            <h5><a class="shop_small_link stip" title="{$p.Beschreibung|tooltip:500}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">{$p.Titel|sanitize}</a></h5>
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
        </div>
        <div class="shop_products_small_clear">&nbsp;</div>
      {/foreach}
      <div style="padding: 3px;"> <a href="index.php?p=shop&amp;action=showproducts&amp;page=1&amp;topseller=1{if !empty($smarty.request.cid)}&amp;cid={$smarty.request.cid}{/if}"> {if !empty($smarty.request.cid)}{#Shop_topsellerAllthisCateg#}{else}{#Shop_topsellerAll#}{/if}</a> </div>
    </div>
  </div>
</div>
{/if}
