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
  {if empty($smarty.session.r_nachname) || empty($smarty.session.r_vorname) || empty($smarty.session.r_email)}
    <form method="post" name="process" id="process" action="https://secure.assist.ru/shops/purchase.cfm">
  {else}
    <form method="post" name="process" id="process" action="https://secure.assist.ru/shops/cardpayment.cfm">
  {/if}
      <input type="hidden" name="Shop_IDP" value="{$payment_data.Install_Id}" />
      <input type="hidden" name="Order_IDP" value="{$smarty.session.id_num_order}" />
      <input type="hidden" name="Subtotal_P" value="{$smarty.session.price_final}" />
      <input type="hidden" name="Language" value="0" />
      <input type="hidden" name="URL_RETURN" value="{$baseurl}/index.php?p=shop" />
      <input type="hidden" name="URL_RETURN_OK" value="{$baseurl}/index.php?payment=as&p=shop&action=callback&reply=success" />
      <input type="hidden" name="URL_RETURN_NO" value="{$baseurl}/index.php?payment=as&p=shop&action=callback&reply=error" />
      <input type="hidden" name="Currency" value="{$payment_data.Testmodus|default:'RUR'}" />
      <input type="hidden" name="Comment" value="{$inf_payment}" />
      <input type="hidden" name="LastName" value="{$smarty.session.r_nachname}" />
      <input type="hidden" name="FirstName" value="{$smarty.session.r_vorname}" />
      <input type="hidden" name="MiddleName" value="{$smarty.session.r_middlename}" />
      <input type="hidden" name="Email" value="{$smarty.session.r_email}" />
      <input type="hidden" name="Address" value="{$smarty.session.r_strasse}" />
      <input type="hidden" name="Phone" value="{$smarty.session.r_telefon}" />
      <input type="hidden" name="City" value="{$smarty.session.r_ort}" />
      <input type="hidden" name="Zip" value="{$smarty.session.r_plz}" />
    </form>
    {else}
      {#Payment_Error#}
      {/if}
