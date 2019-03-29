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
    <div class="popup_content" style="padding: 5px">
    {if $payment_data.Icon}
      <div align="center"><img src="uploads/shop/payment_icons/{$payment_data.Icon}" alt="" /></div>
      <br />
    {/if}
    {$payment_data.Text}
    <br />
    {$payment_data.TextLang}
  </div>
  <form method="post" name="process" id="process" action="https://liqpay.com/?do=clickNbuy" accept-charset="{$charset}">
    <input type="hidden" name="version" value="1.1" />
    <input type="hidden" name="merchant_id" value="{$payment_data.Install_Id}" />
    <input type="hidden" name="amount" value="{$smarty.session.price_final}" />
    <input type="hidden" name="currency" value="{$payment_data.Testmodus}" />
    <input type="hidden" name="description" value="{$inf_payment}" />
    <input type="hidden" name="order_id"  value="{$smarty.session.id_num_order}" />
    <input type="hidden" name="result_url" value="{$baseurl}/index.php?payment=lp&p=shop&action=callback&reply=reset" />
    <input type="hidden" name="server_url" value="{$baseurl}/index.php?payment=lp&p=shop&action=callback&reply=result" />
    <input type="hidden" name="order_number" value="{$smarty.session.order_number}" />
    <input type="hidden" name="hash" value="{$payment_hash}" />
  </form>
  {else}
    {#Payment_Error#}
    {/if}
