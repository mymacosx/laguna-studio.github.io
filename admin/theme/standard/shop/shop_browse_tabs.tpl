<div class="shop_contents_box">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      {foreach from=$tab_items item=p name=pro}
        <td style="text-align: center;">
          {assign var=x_count value=$x_count+1}
          <div class="shop_contents_box_container"><a title="{$p.Titel|sanitize}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}"><img class="shop_productimage_left" src="{$p.Bild_Klein}" alt="{$p.Titel|sanitize}" /></a></div>
          <br />
          {if $shopsettings->PreiseGaeste == 1 || $loggedin}
            <a class="shop_small_link" title="{$p.Titel|sanitize}" href="{$p.ProdLink}{if isset($smarty.request.blanc) && $smarty.request.blanc == 1}&amp;blanc=1{/if}">{$p.Titel|truncate: 20|sanitize}</a>
            <br />
            {if $p.Preis > 0}
              <strong>{$p.Preis|numformat} {$currency_symbol}</strong><span class="sup">*</span>
            {else}
              <strong>{#Zvonite#}</strong>
            {/if}
          {else}
            <strong>{#Shop_prices_justforUsers#}</strong>
          {/if}
        </td>
      {/foreach}
    </tr>
  </table>
</div>
{assign var=x_count value=0}
