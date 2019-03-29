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
    <form method="post" name="process" id="process" action="http://www.z-payment.ru/merchant.php">
      <input type="hidden" name="LMI_PAYEE_PURSE" value="{$payment_data.Install_Id}" />
      <input type="hidden" name="LMI_PAYMENT_AMOUNT" value="{$smarty.session.price_final}" />
      <input type="hidden" name="LMI_PAYMENT_DESC" value="{$inf_payment}" />
      <input type="hidden" name="LMI_PAYMENT_NO" value="{$smarty.session.id_num_order}" />
      <input type="hidden" name="CLIENT_MAIL" value="{$smarty.session.r_email}" />
      <input type="hidden" name="ZP_SIGN" value="{$payment_z_hash}" />
      <input type="hidden" name="FIELD_1" value="{$smarty.session.order_number}" />
      <input type="hidden" name="FIELD_2" value="{$payment_hash}" />
    </form>
  {else}
    {#Payment_Error#}
  {/if}
