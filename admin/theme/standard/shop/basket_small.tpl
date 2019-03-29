<div class="header_basket_header">{#Shop_myBasket#} <img class="absmiddle" src="{$imgpath}/shop/basket_top_cart.png" alt="{#Shop_myBasket#}" /></div>
<div class="header_basket_content">
  <div id="ajaxbasket">
    {include file="$incpath/shop/basket_small_raw.tpl"}
  </div>
</div>
<div class="header_basket_header">{#Login#}</div>
<div class="header_basket_content">
  {if $loggedin}
    <form method="post" name="logout_form_small" action="index.php">
      <input type="hidden" name="p" value="userlogin" />
      <input type="hidden" name="action" value="logout" />
      <input type="hidden" name="area" value="{$area}" />
      <input type="hidden" name="backurl" value="{"index.php"|base64encode}" />
    </form>
    <a href="index.php?p=userlogin">{#MyAccount#}</a> |
    <a href="index.php?p=shop&amp;action=myorders">{#Shop_myAccountOrd#}</a> |
    <a onclick="return confirm('{#Confirm_Logout#}');" href="javascript: document.forms['logout_form_small'].submit();">{#Logout#}</a>
  {else}
    <a href="index.php?p=userlogin">{#LoginExtern#}</a> |
    <a href="index.php?p=register&amp;lang={$langcode}&amp;area={$area}">{#RegNew#}</a>
  {/if}
</div>
