{if !$product_array}
  <p>{#Shop_basket_empty#}</p>
{else}
  {include file="$incpath/shop/basket_items.tpl"}
  <br />
  {include file="$incpath/shop/basket_summ.tpl"}
  <div class="clear"></div>
  {if $status_error}
    <div class="error_box">
      <div class="h2">{#Error#}</div>
      <br />
      {if $status_error == 'to_much'}
        {#Shop_basket_summ_tohigh#} <strong>{$best_max|numformat} </strong>{$currency_symbol}
      {else}
        {#Shop_basket_summ_tolow#} <strong>{$best_min|numformat} </strong>{$currency_symbol}
      {/if}
    </div>
  {else}
    <div class="shop_next_step">
      <form method="post" action="index.php">
        <input type="hidden" name="p" value="shop" />
        <input type="hidden" name="area" value="{$area}" />
        <input type="hidden" name="action" value="shoporder" />
        <input type="hidden" name="subaction" value="step1" />
        <div class="clear"></div>
        <br />
        <input type="submit" class="button" value="{#Shop_go_payment#}" />
      </form>
    </div>
  {/if}
{/if}
