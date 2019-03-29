{include file="$incpath/shop/shop_steps.tpl"}

<div class="clear"></div>
<div class="shop_headers"> {#PleaseSelect#} </div>
<form method="post" action="index.php">
  <div class="shop_data_forms">
    <div class="shop_data_forms_headers"> {#TypeClient#} </div>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td width="10"><input type="radio" name="type_client" id="type_1" value="1" {if !$smarty.session.ship_ok || $smarty.session.type_client == 1}checked="checked"{/if} /></td>
        <td><label for="type_1"><strong> {#TypeClientCl#}</strong></label></td>
      </tr>
      <tr>
        <td width="10"><input type="radio" name="type_client" id="type_2" value="2" {if $smarty.session.type_client == 2}checked="checked"{/if} /></td>
        <td><label for="type_2"><strong> {#Profile_company#} </strong></label></td>
      </tr>
      <tr>
        <td width="10"><input type="radio" name="type_client" id="type_3" value="3" {if $smarty.session.type_client == 3}checked="checked"{/if} /></td>
        <td><label for="type_3"><strong> {#TypeClientIp#} </strong></label></td>
      </tr>
    </table>
  </div>
  <br />
  <br />
  <div class="shop_data_forms">
    <div class="shop_data_forms_headers"> {#ShipOk#}</div>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td width="10"><input type="radio" name="ship_ok" id="ship_1" value="1" {if !$smarty.session.ship_ok || $smarty.session.ship_ok == 1}checked="checked"{/if} /></td>
        <td><label for="ship_1"><strong> {#Yes#} </strong></label></td>
      </tr>
      <tr>
        <td width="10"><input type="radio" name="ship_ok" id="ship_2" value="2" {if $smarty.session.ship_ok == 2}checked="checked"{/if} /></td>
        <td><label for="ship_2"><strong> {#No#} </strong></label></td>
      </tr>
    </table>
  </div>
  <div class="shop_next_step">
    <input type="hidden" name="p" value="shop" />
    <input type="hidden" name="area" value="{$area}" />
    <input type="hidden" name="action" value="shoporder" />
    <input type="hidden" name="subaction" value="step2" />
    <div class="clear"></div>
    <br />
    <input type="submit" class="button" value="{#Shop_nextStep#}" />
</form>
</div>
