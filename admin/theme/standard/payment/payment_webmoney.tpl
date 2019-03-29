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
  <form method="post" name="process" id="process" action="https://merchant.webmoney.ru/lmi/payment.asp">
    <input name="reason" type="hidden" value="{$smarty.session.order_number}" />
    <input name="user_variable_0" type="hidden" value="{$smarty.session.order_number}" />
    <input name="user_variable_1" type="hidden" value="{$smarty.session.price_final|numf}" />
    <input name="hash" type="hidden" value="{$payment_hash}" />
    <input name="rkeys" type="hidden" value="{$smarty.session.order_number}" />
    <input type="hidden" name="LMI_PAYMENT_AMOUNT" value="{$smarty.session.price_final}" />
    <input type="hidden" name="LMI_PAYMENT_DESC" value="{$inf_payment}" />
    <input type="hidden" name="LMI_PAYMENT_NO" value="{$smarty.session.id_num_order}" />
    <input type="hidden" name="LMI_PAYEE_PURSE" value="{$payment_data.Install_Id}" />
    {if $payment_data.Testmodus != 3}
      <input type="hidden" name="LMI_SIM_MODE" value="{$payment_data.Testmodus}" />
    {/if}
    <input type="hidden" name="LMI_RESULT_URL" value="{$baseurl}/index.php?payment=wm&p=shop&action=callback&reply=result" />
    <input type="hidden" name="LMI_SUCCESS_URL" value="{$baseurl}/index.php?payment=wm&p=shop&action=callback&reply=success" />
    <input type="hidden" name="LMI_SUCCESS_METHOD" value="2" />
    <input type="hidden" name="LMI_FAIL_URL" value="{$baseurl}/index.php?payment=wm&p=shop&action=callback&reply=error" />
    <input type="hidden" name="LMI_FAIL_METHOD" value="2" />
  </form>
{else}
  {#Payment_Error#}
{/if}
