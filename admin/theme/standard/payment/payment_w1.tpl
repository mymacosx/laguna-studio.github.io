{include file="$incpath/shop/shop_steps.tpl"}
<br />
{if empty($payment_error)}
<script type="text/javascript">
<!-- //
$(window).load(function() {
    setTimeout(function() {
        $('#process').submit();
    }, 2500);
});
//-->
</script>

    <strong>{#Shop_thankyou_title#}</strong>
    <br />
    <br />
    <div class="popup_content padding5">
      {if $payment_data.Icon}
        <div align="center"><img src="uploads/shop/payment_icons/{$payment_data.Icon}" alt="" /></div>
        <br />
      {/if}
      {$payment_data.Text}
      <br />
      {$payment_data.TextLang}
    </div>
    <form method="post" name="process" id="process" action="https://merchant.w1.ru/checkout/default.aspx" accept-charset="{$charset}">
      {foreach from=$payment_array key=k item=v}
        <input type="hidden" name="{$k}" value="{$v}" />
      {/foreach}
    </form>
  {else}
    {#Payment_Error#}
  {/if}
