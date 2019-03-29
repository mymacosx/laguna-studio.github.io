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
    <div class="popup_content padding5"> {if $payment_data.Icon}
      <div align="center"><img src="uploads/shop/payment_icons/{$payment_data.Icon}" alt="" /></div>
      <br />
    {/if}
    {$payment_data.Text}
    <br />
    {$payment_data.TextLang}
  </div>
  {* <form method="post" name="process" id="process" action="http://test.robokassa.ru/Index.aspx"> тестовый сервер *}
  <form method="post" name="process" id="process" action="https://merchant.roboxchange.com/Index.aspx">
    <input type=hidden name="MrchLogin" value="{$payment_data.Install_Id}" />
    <input type=hidden name="OutSum" value="{$smarty.session.price_final}" />
    <input type=hidden name="InvId" value="{$smarty.session.id_num_order}" />
    <input type=hidden name="Desc" value="{$inf_payment}" />
    <input type=hidden name="SignatureValue" value="{$crc}" />
    <input type=hidden name="IncCurrLabel" value="" />
    <input type=hidden name="Culture" value="ru" />
    <input type=hidden name="Shp_order" value="{$smarty.session.order_number}" />
    <input type=hidden name="Shp_hash" value="{$payment_hash}" />
  </form>
  {else}
  {#Payment_Error#}
  {/if}
