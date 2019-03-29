{include file="$incpath/shop/shop_steps.tpl"}

{if $sx_error}
  <div class="error_box">
    <div class="h2">{#Error#}</div>
    <br />
    {if $sx_error == 'to_much'}
      {#Shop_basket_summ_tohigh#} <strong>{$best_max|numformat} </strong>{$currency_symbol}.
    {else}
      {#Shop_basket_summ_tolow#} <strong>{$best_min|numformat} </strong>{$currency_symbol}.
    {/if}
  </div>
{else}
  {if $error == 1}
    <div class="shop_headers">{#Shop_step_3#}</div>
    <div class="error_box">{#Shop_noshipper_thisweight#}</div>
{else}
<div class="clear"></div>
<form method="post" action="index.php" name="refresh">
  <input type="hidden" name="p" value="shop" />
  <input type="hidden" name="area" value="{$area}" />
  <input type="hidden" name="action" value="shoporder" />
  <input type="hidden" name="subaction" value="step3" />
  {if $smarty.request.order == "guest"}
  <input type="hidden" name="order" value="guest" />
  {/if}
  <input type="hidden" name="versand_id" id="hvid" value="" />
</form>
<form onsubmit="return checkbank();" method="post" name="shipper_selection" action="index.php">
  <div class="shop_headers">{#Shop_step_3#}</div>
  {if $smarty.session.ship_ok == 1}
  <div class="shop_data_forms">
    <div class="shop_data_forms_headers">{#Shop_select_shipper#}</div>
    {if $shipper_found == 1}
    {if !$shipper}
    {#Shop_noshipper_thisweight#}
    {else}
<script type="text/javascript">
<!-- //
function refresh_method(VID) {
    document.getElementById('hvid').value = VID;
    document.forms['refresh'].submit();
}
//-->
</script>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      {foreach from=$shipper item=s name="slister"}
      {if $smarty.session.ship_ok == 1 || $s->Id == 1 || $s->Id == 2}
      <tr>
        <td class="shop_payments_rows" width="10" valign="top"><input onclick="refresh_method('{$s->Id}');" type="radio" name="versand_id" id="versand_id_{$s->Id}" value="{$s->Id}" {if !isset($smarty.request.versand_id)}{if $smarty.foreach.slister.first}checked="checked"{/if}{else}{if $smarty.request.versand_id == $s->Id}checked="checked"{/if}{/if} /></td>
        <td class="shop_payments_rows"><label for="versand_id_{$s->Id}"><strong>{$s->Name}</strong></label>
          <br />
          {if $s->Icon} <img src="uploads/shop/shipper_icons/{$s->Icon}" alt="" border="0" /> <br />
          {/if}
          {$s->Beschreibung} </td>
      </tr>
      {/if}
      {/foreach}
    </table>
    <noscript>
    &nbsp;
    <input class="button" type="submit" value="{#Shop_referesh_shipper#}" />
    </noscript>
    <br />
    {if $smarty.session.ship_ok == 1}
    <div align="right"> <strong>{#Shop_shipper_costs#}</strong>&nbsp;
      {if $shipping_summ}
      {$shipping_summ|numformat} {$currency_symbol}
      {else}
      {$shipperInf->Gebuehr|numformat} {$currency_symbol}
      {/if} </div>
    {/if}
    {/if}
    {else}
    {#Shop_noshipper_thisCountry#}
    {/if} </div>
  {/if}
  {if $Payments} <a name="payments"></a> <br />
  <div class="shop_data_forms">
    <div class="shop_data_forms_headers">{#Shop_payment_methods#}</div>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      {foreach name=payment from=$Payments item=pm}
      {if $pm->Id != 1 || $smarty.session.type_client != 1}
      <tr>
        <td width="10" valign="top" class="shop_payments_rows"><a name="vid{$pm->Id}"></a> {if $pm->MaxWert > 0 && $smarty.session.price > $pm->MaxWert}
          <input type="radio" name="payment_id" value="123456789" id="pid_12345" disabled="disabled" />
          {else}
          <input type="radio" name="payment_id" value="{$pm->Id}" id="pid_{$pm->Id}" {if $smarty.request.payment_id == $pm->Id}checked="checked"{else}{if $smarty.foreach.payment.first}checked="checked"{/if}{/if} />
          {/if} </td>
        <td valign="top" class="shop_payments_rows"><label for="pid_{$pm->Id}">
          {if $pm->MaxWert > 0 && $smarty.session.price > $pm->MaxWert} <span style="text-decoration: line-through"> {$pm->Name}
          {if $pm->Kosten != '0.00'} <em> {if $pm->KostenTyp == 'pro'}
          {$pm->KostenOperant} {$pm->Kosten}%
          {else}
          {if $pm->KostenOperant == '+'}
          {#Shop_f_zzgl#}
          {else}
          -
          {/if}
          {$pm->Kosten|numformat}
          {$currency_symbol}
          {/if} </em> {/if} </span>
          <div class="error_box"> {assign var=lockedMsg value=$lang.Shop_paymentmethod_locked|replace: '__WERT__': $pm->MaxWert}
            {$lockedMsg|replace: '__WAEHRUNG__': $currency_symbol} </div>
          {else} <strong>{$pm->Name}</strong> {if $pm->Kosten != '0.00'} <em> {if $pm->KostenTyp == 'pro'}
          {$pm->KostenOperant} {$pm->Kosten}
          %
          {else}
          {if $pm->KostenOperant == '+'}
          {#Shop_f_zzgl#}
          {else}
          -
          {/if}
          {$pm->Kosten|numformat} {$currency_symbol}
          {/if} </em> {/if}
          {/if}
          </label>
          <div id="descr_{$pm->Id}">
            <div id="show_{$pm->Id}" style="display: none"><a href="javascript: show_shipping_inf('{$pm->Id}','hide');">{#Shop_za_out#}</a></div>
            <div id="hide_{$pm->Id}" style="display: none"><a href="javascript: show_shipping_inf('{$pm->Id}');">{#Shop_za_in#}</a></div>
            <br />
            {if $pm->Icon} <img src="uploads/shop/payment_icons/{$pm->Icon}" border="0" alt="" /> <br />
            <br />
            {/if}
            {$pm->Beschreibung} </div></td>
        <td width="140" align="right" valign="top" nowrap="nowrap" class="shop_payments_rows"><a class="colorbox_small" href="index.php?p=misc&amp;do=payment_info&amp;id={$pm->Id}">{#Shop_za_in#}</a></td>
      </tr>
      {/if}
      {/foreach}
    </table>
  </div>
  {else}
  {if $shipper_found && $shipper}
    <br />
  <div class="shop_data_forms">
    <div class="shop_data_forms_headers">{#Shop_payment_methods#}</div>
    {#Shop_noPayment#}
  </div>
  {/if}
  {/if}
    {if $Payments}
      <br />
      <div class="shop_next_step">
        <input type="hidden" name="p" value="shop" />
        <input type="hidden" name="area" value="{$area}" />
        <input type="hidden" name="action" value="shoporder" />
        <input type="hidden" name="subaction" value="step4" />
        {if $smarty.request.order == 'guest'}
          <input type="hidden" name="order" value="guest" />
        {/if}
        <input type="submit" class="button" value="{#Shop_nextStep#}" />
      </div>
    {/if}
  </form>
  {/if}
{/if}
