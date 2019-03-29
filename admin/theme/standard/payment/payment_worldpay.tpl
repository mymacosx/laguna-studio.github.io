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
    <form action="https://select.worldpay.com/wcc/purchase" method="post" name="process" id="process">
      <input name="instId" type="hidden" value="{$payment_data.Install_Id}" />
      <input name="cartId" type="hidden" value="{$smarty.session.order_number}" />
      <input name="amount" type="hidden" value="{$smarty.session.price_final}" />
      <input name="currency" type="hidden" value="{$smarty.session.currency_registered}" />
      <input name="desc" type="hidden" value="{$payment_data.Betreff}" />
      <input name="testMode" type="hidden" value="{$payment_data.Testmodus}" />
      <input name="M_uid" type="hidden" value="{$smarty.session.benutzer_id}" />
      <input name="M_hid" type="hidden" value="{$smarty.session.order_number}" />
      <input name="M_articles" type="hidden" value="{$smarty.session.products}" />
      <input name="name" type="hidden" value="{$smarty.session.r_vorname} {$smarty.session.r_nachname}" />
      <input name="address" type="hidden" value="{if $smarty.session.r_firma}{$smarty.session.r_firma} - {/if}{$smarty.session.r_vorname} {$smarty.session.r_nachname} {$smarty.session.r_strasse} {$smarty.session.r_plz} {$smarty.session.r_ort}" />
      <input name="postcode" type="hidden" value="{$smarty.session.r_plz}" />
      <input name="tel" type="hidden" value="{$smarty.session.r_telefon}" />
      <input name="fax" type="hidden" value="{$smarty.session.r_fax}" />
      <input name="email" type="hidden" value="{$smarty.session.r_email}" />
      <input name="country" type="hidden" value="{$shop_land}" />
    </form>
  {else}
    {#Payment_Error#}
  {/if}
