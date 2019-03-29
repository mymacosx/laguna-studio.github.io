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
  <form action="https://www.moneybookers.com/app/payment.pl" method="post" name="process" id="process">
    <input type="hidden" name="pay_to_email" value="{$payment_data.Install_Id}" />
    <input type="hidden" name="pay_from_email" value="{$smarty.session.r_email}" />
    <input type="hidden" name="transaction_id" value="{$smarty.session.order_number}" />
    <input type="hidden" name="firstname" value="{$smarty.session.r_vorname}" />
    <input type="hidden" name="lastname" value="{$smarty.session.r_nachname}" />
    <input type="hidden" name="address" value="{$smarty.session.r_strasse}" />
    <input type="hidden" name="city" value="{$smarty.session.r_ort}" />
    <input type="hidden" name="country" value="" />
    <input type="hidden" name="confirmation_note" value="Спасибо за оказанное доверие!" />
    <input type="hidden" name="status_url" value="{$shopsettings->Email_Abs}" />
    <input type="hidden" name="language" value="{$shopsettings->ShopLand|upper|default:'RU'}" />
    <input type="hidden" name="amount" value="{$smarty.session.price_final}" />
    <input type="hidden" name="currency" value="{$smarty.session.currency_registered|default:'RUR'}" />
    <input type="hidden" name="detail1_description" value="{$payment_data.Betreff}" />
    <input type="hidden" name="detail1_text" value="" />
  </form>
  {else}
    {#Payment_Error#}
    {/if}
