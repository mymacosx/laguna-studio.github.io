{include file="$incpath/shop/shop_steps.tpl"}
<br />
{if empty($payment_error)}
<script type="text/javascript">
<!-- //
$(window).load(function () {
    setTimeout(function () {
        $('#easypay_payment_form').submit();
    }, 2500);
});
//-->
</script>

    <strong>{#Shop_thankyou_title#}</strong>
    <br />
    <br />
    <div class="popup_content" style="padding: 5px">
      {if $payment_data.Icon}
          <div align="center"><img src="uploads/shop/payment_icons/{$payment_data.Icon}" alt="" /></div>
          <br />
      {/if}
      {$payment_data.Text}
      <br />
      {$payment_data.TextLang}
    </div>
    <form method="post" method="post" id="easypay_payment_form" action="https://ssl.easypay.by/weborder/">
      <input type="hidden" name="EP_MerNo" value="{$EP_MerNo}" />
      <input type="hidden" name="EP_Expires" value="{$EP_Expires}" />
      <input type="hidden" name="EP_Debug" value="{$EP_Debug}" />
      <input type="hidden" name="EP_Sum" value="{$EP_Sum}" />
      <input type="hidden" name="EP_OrderNo" value="{$EP_OrderNo}" />
      <input type="hidden" name="EP_OrderInfo" value="{$EP_OrderInfo}" />
      <input type="hidden" name="EP_Hash" value="{$EP_Hash}" />
      <input type="hidden" name="EP_URL_Type" value="get" />
      <input type="hidden" name="EP_Success_URL" value="{$baseurl}" />
      <input type="hidden" name="EP_Cancel_URL"	value="{$baseurl}" />
    </form>
{else}
    {#Payment_Error#}
{/if}
