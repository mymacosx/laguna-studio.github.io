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
    <div class="popup_content" style="padding: 5px"> {if $payment_data.Icon}
      <div align="center"><img src="uploads/shop/payment_icons/{$payment_data.Icon}" alt="" /></div>
      <br />
    {/if}
    {$payment_data.Text}
    <br />
    {$payment_data.TextLang}
  </div>
  <form method="post" name="process" id="process" action="http://www.interkassa.com/lib/payment.php">
    <input type="hidden" name="ik_shop_id" value="{$payment_data.Install_Id}" />
    <input type="hidden" name="ik_payment_amount" value="{$smarty.session.price_final}" />
    <input type="hidden" name="ik_payment_id" value="{$smarty.session.id_num_order}" />
    <input type="hidden" name="ik_payment_desc" value="{$inf_payment}" />
    <input type="hidden" name="ik_paysystem_alias" value="" />
    <input type="hidden" name="ik_baggage_fields" value="{$payment_hash}" />
    <input type="hidden" name="ik_sign_hash" value="{$ik_hash}" />
  </form>
  {else}
    {#Payment_Error#}
    {/if}
