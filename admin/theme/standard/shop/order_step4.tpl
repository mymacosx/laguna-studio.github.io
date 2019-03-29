{include file="$incpath/shop/shop_steps.tpl"}

<div class="clear"></div>
<div class="shop_headers">{#Shop_text_data#} </div>
{if $agb_error == 1}
  <div class="error_box"> {#Shop_agb_accept_f#} </div>
{/if}
<div class="shop_data_forms"> {include file="$incpath/shop/basket_items.tpl"}
  {include file="$incpath/shop/basket_summ.tpl"}
  <div class="clear"> </div>
</div>
<br />
<form method="post" name="rla" action="index.php#diff_adress">
  <input type="hidden" name="p" value="shop" />
  <input type="hidden" name="area" value="{$area}" />
  <input type="hidden" name="action" value="shoporder" />
  <input type="hidden" name="subaction" value="step2" />
  {if !$loggedin}
    <input type="hidden" name="order" value="guest" />
  {/if}
</form>
<div class="shop_headers">{#Shop_f_shipping_billing_adress#} (<a href="javascript: document.forms['rla'].submit();"><font color="#000000">{#Shop_f_change#}</font></a>)</div>
<div class="shop_data_forms">
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%" valign="top"><div class="shop_data_forms_headers">{#Shop_f_billingadress#}</div></td>
      <td valign="top">&nbsp;&nbsp;</td>
      <td width="50%" valign="top">
        {if $smarty.session.ship_ok == 1}
          <div class="shop_data_forms_headers">{#Shop_f_shippingadress#}</div>
        {/if}
      </td>
    </tr>
    <tr>
      <td valign="top">
        {if !empty($smarty.session.r_nachname) || !empty($smarty.session.r_vorname)}
          <strong>{#Profile_Buyer#}: </strong> {$smarty.session.r_nachname} {$smarty.session.r_vorname}
          {if !empty($smarty.session.r_middlename)} {$smarty.session.r_middlename}{/if}
          <br />
          <br />
        {/if}
        {if !empty($smarty.session.r_email)}
          <strong>{#Email#}: </strong> {$smarty.session.r_email}
          <br />
        {/if}
        {if !empty($smarty.session.r_telefon)}
          <strong>{#Phone#}: </strong> {$smarty.session.r_telefon}
          <br />
        {/if}
        {if !empty($smarty.session.r_fax)}
          <strong>{#Fax#}: </strong> {$smarty.session.r_fax}
          <br />
        {/if}
        {if !empty($smarty.session.r_land_lang)}
          <strong>{#Country#}: </strong> {$smarty.session.r_land_lang}
          <br />
        {/if}
        {if !empty($smarty.session.r_plz)}
          <strong>{#Profile_Zip#}: </strong> {$smarty.session.r_plz}
          <br />
        {/if}
        {if !empty($smarty.session.r_ort)}
          <strong>{#Town#}: </strong> {$smarty.session.r_ort}
          <br />
        {/if}
        {if !empty($smarty.session.r_strasse)}
          <strong>{#Profile_Street#}: </strong> {$smarty.session.r_strasse}
          <br />
          <br />
        {/if}
        {if !empty($smarty.session.r_firma)}
          <strong>{#Profile_company#}: </strong> {$smarty.session.r_firma}
          <br />
        {/if}
        {if !empty($smarty.session.r_ustid)}
          <strong>{#Profile_vatnum#}: </strong> {$smarty.session.r_ustid}
          <br />
        {/if}
        {if $settings.Reg_Bank == 1 && !empty($smarty.session.r_ustid)}
          <strong>{#Profile_Bank#}: </strong>
          <br />
          {$smarty.session.r_bankname}
          <br />
        {/if}
      </td>
      <td valign="top">&nbsp;&nbsp; </td>
      <td valign="top">
        {if $smarty.session.ship_ok == 1}
          {if $smarty.session.diff_rl == 'liefer_gleich'}
            {#Shop_f_same_sa#}
            <br />
          {else}
            {if !empty($smarty.session.l_nachname) || !empty($smarty.session.l_vorname)}
              <strong>{#Recipient#}: </strong> {$smarty.session.l_nachname} {$smarty.session.l_vorname}
              {if !empty($smarty.session.l_middlename)} {$smarty.session.l_middlename}{/if}
              <br />
            {/if}
            {if !empty($smarty.session.l_firma)}
              <strong>{#Profile_company#}: </strong> {$smarty.session.l_firma}
              <br />
              <br />
            {/if}
            {if !empty($smarty.session.l_telefon)}
              <strong>{#Phone#}: </strong> {#Phone#}: {$smarty.session.l_telefon}
              <br />
            {/if}
            {if !empty($smarty.session.l_fax)}
              <strong>{#Fax#}: </strong> {$smarty.session.l_fax}
              <br />
              <br />
            {/if}
            {if !empty($smarty.session.l_land_lang)}
              <strong>{#Country#}: </strong> {$smarty.session.l_land_lang}
              <br />
            {/if}
            {if !empty($smarty.session.l_plz)}
              <strong>{#Profile_Zip#}: </strong> {$smarty.session.l_plz}
              <br />
            {/if}
            {if !empty($smarty.session.l_ort)}
              <strong>{#Town#}: </strong> {$smarty.session.l_ort}
              <br />
            {/if}
            {if !empty($smarty.session.l_strasse)}
              <strong>{#Profile_Street#}: </strong> {$smarty.session.l_strasse}
              <br />
            {/if}
          {/if}
        {/if}
      </td>
    </tr>
  </table>
  <br />
  {if !empty($smarty.session.r_nachricht)}
    <div class="shop_data_forms_headers">{#Shop_f_ordermessage#}</div>
    {$smarty.session.r_nachricht}
  {/if}
</div>
<br />
<form method="post" name="vza" action="index.php">
  <input type="hidden" name="p" value="shop" />
  <input type="hidden" name="area" value="{$area}" />
  <input type="hidden" name="action" value="shoporder" />
  <input type="hidden" name="subaction" value="step1" />
  {if !$loggedin}
    <input type="hidden" name="order" value="guest" />
  {/if}
</form>
<div class="shop_headers">{#Shop_f_shipping_billing#} (<a href="javascript: document.forms['vza'].submit();"><font color="#000000">{#Shop_f_change#}</font></a>)</div>
<div class="shop_data_forms">
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td width="50%" valign="top"><div class="shop_data_forms_headers">{#Shop_f_shipping_method#}</div></td>
      <td valign="top">&nbsp;&nbsp;</td>
      <td width="50%" valign="top"><div class="shop_data_forms_headers">{#Shop_payment_methods#}</div></td>
    </tr>
    <tr>
      <td valign="top">{if $smarty.session.ship_ok == 1}{$smarty.session.shipper_name}{else}{#No#}{/if}</td>
      <td valign="top">&nbsp;</td>
      <td valign="top">{$smarty.session.payment_name}</td>
    </tr>
  </table>
</div>
<br />
<a name="return" id="return"></a>
<div class="shop_headers">{#Shop_f_rcall_inf#}</div>
<div class="shop_data_forms" style="height: 200px; overflow: auto"> {$widerruf_belehrung} </div>
<br />
{include file="$incpath/shop/send_order_form.tpl"}
