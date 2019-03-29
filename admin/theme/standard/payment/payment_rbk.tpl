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
    <form method="post" name="process" id="process" action="https://rbkmoney.ru/acceptpurchase.aspx">
      <input type="hidden" name="eshopId" value="{$payment_data.IdSeller}" />
      <input type="hidden" name="orderId" value="{$smarty.session.id_num_order}" />
      <input type="hidden" name="serviceName" value="{$inf_payment}" />
      <input type="hidden" name="recipientAmount" value="{$smarty.session.price_final}" />
      <input type="hidden" name="recipientCurrency" value="{$payment_data.Testmodus}" />
      <input type="hidden" name="user_email" value="{$smarty.session.price_final}" />
      <input type="hidden" name="version" value="2" />
      <input type="hidden" name="successUrl" value="{$baseurl}/index.php?payment=rb&p=shop&action=callback&reply=success" />
      <input type="hidden" name="failUrl" value="{$baseurl}/index.php?payment=rb&p=shop&action=callback&reply=error" />
      <input type="hidden" name="userField_1" value="{$smarty.session.order_number}" />
      <input type="hidden" name="userField_2" value="{$payment_hash}" />
    </form>
  {else}
    {#Payment_Error#}
  {/if}
