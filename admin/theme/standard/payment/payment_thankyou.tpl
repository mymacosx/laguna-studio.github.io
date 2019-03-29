{include file="$incpath/shop/shop_steps.tpl"}
<br />
<strong>{#Shop_thankyou_title#}</strong>
<br />
<br />
{#Shop_order_thankyou#}
<br />
{if !empty($smarty.session.DetailInfo)}
  {$smarty.session.DetailInfo}
  <br />
{/if}
<div align="center">
  <form method="post" action="index.php?p=shop">
    <input type="submit" class="button" value="{#Shop_back_shop#}" />
  </form>
</div>
