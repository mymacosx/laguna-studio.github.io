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
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="process" id="process">
  <input type="hidden" name="cmd" value="_xclick" />
  <input type="hidden" name="business" value="{$payment_data.Install_Id}" />
  <input type="hidden" name="item_name" value="{$payment_data.Betreff}" />
  <input type="hidden" name="item_number" value="{$smarty.session.order_number}" />
  <input type="hidden" name="amount" value="{$smarty.session.price_final}" />
  <input type="hidden" name="no_shipping" value="1" />
  <input type="hidden" name="return" value="" />
  <input type="hidden" name="no_note" value="1" />
  <input type="hidden" name="cancel_return" value="{$smarty.session.back_url}" />
  <input type="hidden" name="return" value="{$baseurl}/paypal.php" />
  <input type="hidden" name="currency_code" value="{$smarty.session.currency_registered}" />
</form>
{else}
  {#Payment_Error#}
  {/if}
