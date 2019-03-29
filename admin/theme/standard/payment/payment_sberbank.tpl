<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.mydownloads').colorbox({ height: "95%", width: "80%", iframe: true });
});
//-->
</script>

{include file="$incpath/shop/shop_steps.tpl"}
<br />
{if $payment_error != 1} <strong>{#Shop_thankyou_title#}</strong>
  <br />
  <br />
  Квитанция оформлена, распечатать Вы ее можете, нажав на ссылку.
  <br />
  <br />
  <a class="mydownloads" href="index.php?p=misc&do=viewpayorder&oid={$smarty.session.id_num_order}" title="Печать квитанции"><img class="absmiddle" alt="" border="0" src="{$imgpath}/shop/p_print.png" /></a>&nbsp;&nbsp;
  <a class="mydownloads" href="index.php?p=misc&do=viewpayorder&oid={$smarty.session.id_num_order}" title="Печать квитанции">Распечатать квитанцию</a>
  <br />
  <br />
  Так же квитанцию для оплаты можно распечатать в разделе <a title="{#Shop_go_myorders#}" href="index.php?p=shop&amp;action=myorders">{#Shop_go_myorders#}</a>
  <br />
  <br />
  {include file="$incpath/payment/payment_pd4.tpl"}
{else}
  {#Payment_Error#}
{/if}
