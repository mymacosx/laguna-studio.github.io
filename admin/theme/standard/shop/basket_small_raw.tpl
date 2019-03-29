{if (isset($basket_products_price) && $basket_products_price > 0) || (isset($basket_products_all) && $basket_products_all >= 1)}
  {if $smarty.request.subaction != 'step4' && $smarty.request.subaction != 'final'}
    <div class="header_basket_elems">{$basket_products|default:0} {#Shop_f_articles#}&nbsp; - &nbsp;{$basket_products_price|numformat} {$currency_symbol}</div>
  {/if}
  <div style="text-align: center">
    <button type="button" class="shop_buttons_big" style="width: 95px; margin-bottom: 3px" onclick="{if $basket_products_price > 0 || $basket_products_all >= 1}location.href = '{$baseurl}/index.php?p=shop&amp;action=showbasket';{else}javascript: alert('{#Shop_basket_empty#}'){/if}">{#Shop_myBasket#}</button>
    <button type="button" class="shop_buttons_big_second" style="width: 100px; margin-bottom: 3px" onclick="{if $basket_products_price > 0 || $basket_products_all >= 1}location.href = '{$baseurl}/index.php?p=shop&amp;action=shoporder&amp;step=2';{else}javascript: alert('{#Shop_basket_empty#}'){/if}">{#Shop_go_payment#}</button>
  </div>
  <br />
{else}
  {#Shop_basket_empty#}
  <br />
  <br />
{/if}
