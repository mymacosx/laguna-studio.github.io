{#Shop_priceAlertInf#}
<br />
<br />
{if !empty($error)}
  <div class="error_box">
    <ul>
      {foreach from=$error item=err}
        <li>{$err}</li>
        {/foreach}
    </ul>
  </div>
{/if}
<form method="post" action="#pricealert">
  <label>{#Email#}: &nbsp;<input type="text" class="input" name="pricealert_email" value="{$smarty.post.pricealert_email|sanitize}" /></label>&nbsp;&nbsp;
  <label>{#Shop_priceAlertYPrice#}: &nbsp;<input type="text" class="input" name="pricealert_newprice" value="{$smarty.post.pricealert_newprice|sanitize}"/></label>&nbsp;
  <input type="submit" class="button" value="{#ButtonSend#}" />
  <input type="hidden" name="pricealert_send" value="1" />
  <input type="hidden" name="red" value="{$red}" />
</form>
