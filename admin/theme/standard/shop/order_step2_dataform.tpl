{if $status_error}
  <div class="error_box">
    <div class="h2">{#Error#}</div>
    <br />
    {if $status_error == 'to_much'}
      {#Shop_basket_summ_tohigh#} <strong>{$best_max|numformat} </strong>{$currency_symbol}.
    {else}
      {#Shop_basket_summ_tolow#} <strong>{$best_min|numformat} </strong>{$currency_symbol}.
    {/if}
  </div>
{else}
  {if $shop_coupons == 1 && $smarty.session.price > 0}
    <div class="shop_basket_header">{#Shop_o_coupon#}</div>
    <div class="infobox" id="ajaxcpresult">
      {if $smarty.session.coupon_code}
        {include file="$incpath/shop/coupon-ajaxdel.tpl"}
      {else}
        {include file="$incpath/shop/coupon-ajaxinsert.tpl"}
      {/if}
    </div>
  {/if}
  <div class="shop_headers">{#Shop_step_2#}</div>
  <div class="shop_data_forms">
    {if $r_errors}
      <div class="error_box">
        <ul>
          {foreach from=$r_errors item=error}
            <li>{$error}</li>
            {/foreach}
        </ul>
      </div>
    {/if}
    <div class="shop_data_forms_headers">{#Shop_f_billingadress#} <span style="font-weight: normal">{#Shop_fillout_required#}</span></div>
    <form method="post" action="index.php">
      <table width="100%" cellpadding="2" cellspacing="0" class="box_inner">
        {if $smarty.session.ship_ok == 1 || $settings.Reg_DataPflicht == 1}
          <tr>
            <td width="200"><label for="l_r_nachname">{#LastName#}</label>&nbsp;</td>
            <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_DataPflichtFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
            <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_nachname" type="text" id="l_r_nachname" value="{if empty($smarty.post.r_nachname)}{$smarty.session.benutzer_nachname|default:$smarty.session.r_nachname}{else}{$smarty.post.r_nachname|escape: html|default:$smarty.session.r_nachname}{/if}" /></td>
          </tr>
          <tr>
            <td width="200"><label for="l_r_vorname">{#GlobalName#}</label>&nbsp;</td>
            <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_DataPflichtFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
            <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_vorname" type="text" id="l_r_vorname" value="{if empty($smarty.post.r_vorname)}{$smarty.session.benutzer_vorname|default:$smarty.session.r_vorname}{else}{$smarty.post.r_vorname|escape: html|default:$smarty.session.r_vorname}{/if}" /></td>
          </tr>
          <tr>
            <td width="200"><label for="l_r_middlename">{#Profile_MiddleName#}</label>&nbsp;</td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_middlename" type="text" id="l_r_middlename" value="{if empty($smarty.post.r_middlename)}{$smarty.session.benutzer_middlename|default:$smarty.session.r_middlename}{else}{$smarty.post.r_middlename|escape: html|default:$smarty.session.r_middlename}{/if}" /></td>
          </tr>
        {/if}
        <tr>
          <td width="200"><label for="l_r_email">{#Email#}</label>&nbsp;</td>
          <td width="5"><sup>*</sup></td>
          <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_email" type="text" id="l_r_email" value="{$smarty.post.r_email|escape: html|default:$smarty.session.r_email}" /></td>
        </tr>
        {if $settings.Reg_Fon == 1 || $shopsettings->Telefon_Pflicht}
          <tr>
            <td width="200"><label for="l_r_telefon">{#Phone#}</label>
              &nbsp;</td>
            <td width="5">{if $shopsettings->Telefon_Pflicht}<sup>*</sup>{/if}</td>
            <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_telefon" type="text" id="l_r_telefon" value="{if empty($smarty.post.r_telefon)}{$smarty.session.benutzer_fon|default:$smarty.session.r_telefon}{else}{$smarty.post.r_telefon|escape: html|default:$smarty.session.r_telefon}{/if}" /></td>
          </tr>
        {/if}
        {if $settings.Reg_Fax == 1}
          <tr>
            <td width="200"><label for="l_r_fax">{#Fax#}</label>&nbsp;</td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_fax" type="text" id="l_r_fax" value="{if empty($smarty.post.r_fax)}{$smarty.session.benutzer_fax|default:$smarty.session.r_fax}{else}{$smarty.post.r_fax|escape: html|default:$smarty.session.r_fax}{/if}" /></td>
          </tr>
        {/if}
        <tr>
          <td width="200"><label for="l_r_land">{#Country#}</label>&nbsp;</td>
          <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
          <td nowrap="nowrap">
            <select class="input" name="r_land" id="l_r_land" style="width: 210px">
              {foreach from=$countries item=c}
                <option value="{$c.Code}" {if $smarty.request.save == 1}{if $smarty.request.r_land == $c.Code}selected="selected"{/if}{else}{if $smarty.session.r_land == $c.Code}selected="selected"{else}{if $smarty.request.save != '1' && $shop_country == $c.Code && !$smarty.session.r_land}selected="selected"{elseif $smarty.request.r_land == $c.Code}selected="selected"{/if}{/if}{/if}>
                  {$c.Name}
                </option>
              {/foreach}
            </select>
          </td>
        </tr>
        {if $smarty.session.ship_ok == 1 || $settings.Reg_Address == 1}
          <tr>
            <td width="200"><label for="l_r_plz">{#Profile_Zip#}</label>&nbsp;</td>
            <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
            <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_plz" type="text" id="l_r_plz" value="{if empty($smarty.post.r_plz)}{$smarty.session.benutzer_plz|default:$smarty.session.r_plz}{else}{$smarty.post.r_plz|escape: html|default:$smarty.session.r_plz}{/if}" /></td>
          </tr>
          <tr>
            <td width="200"><label for="l_r_ort">{#Town#}</label>&nbsp;</td>
            <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
            <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_ort" type="text" id="l_r_ort" value="{if empty($smarty.post.r_ort)}{$smarty.session.benutzer_ort|default:$smarty.session.r_ort}{else}{$smarty.post.r_ort|escape: html|default:$smarty.session.r_ort}{/if}" /></td>
          </tr>
          <tr>
            <td width="200"><label for="l_r_strasse">{#Profile_Street#}</label>&nbsp;</td>
            <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
            <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_strasse" type="text" id="l_r_strasse" value="{if empty($smarty.post.r_strasse)}{$smarty.session.benutzer_strasse|default:$smarty.session.r_strasse}{else}{$smarty.post.r_strasse|escape: html|default:$smarty.session.r_strasse}{/if}" /></td>
          </tr>
        {/if}
        {if $settings.Reg_Firma == 1 && $smarty.session.type_client != 1}
          <tr>
            <td width="200"><label for="l_r_firma">{#Profile_company#}</label>&nbsp;</td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_firma" type="text" id="l_r_firma" value="{if empty($smarty.post.r_firma)}{$smarty.session.benutzer_firma|default:$smarty.session.r_firma}{else}{$smarty.post.r_firma|escape: html|default:$smarty.session.r_firma}{/if}" /></td>
          </tr>
        {/if}
        {if $settings.Reg_Ust == 1 && $smarty.session.type_client != 1}
          <tr>
            <td width="200"><label for="l_r_ustid">{#Profile_vatnum#}<strong></label>&nbsp;</td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap"><input class="input" style="width: 200px" name="r_ustid" type="text" id="l_r_ustid" value="{if empty($smarty.post.r_ustid)}{$smarty.session.benutzer_ustid|default:$smarty.session.r_ustid}{else}{$smarty.post.r_ustid|escape: html|default:$smarty.session.r_ustid}{/if}" /></td>
          </tr>
        {/if}
        {if $settings.Reg_Bank == 1 && $smarty.session.type_client != 1}
          <tr>
            <td width="200"><label for="l_r_bankname">{#Profile_Bank#}</label>&nbsp;</td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap"><textarea name="r_bankname" cols="" rows="" class="input" id="l_r_bankname" style="width: 350px; height: 100px">{$smarty.post.r_bankname|escape: html|default:$smarty.session.r_bankname}</textarea></td>
          </tr>
        {/if}
        <tr>
          <td width="200"><label for="l_r_nachricht">{#Shop_f_ordermessage#}</label>&nbsp;</td>
          <td width="5">&nbsp;</td>
          <td nowrap="nowrap"><textarea name="r_nachricht" cols="" rows="" class="input" id="l_r_nachricht" style="width: 350px; height: 150px">{$smarty.post.r_nachricht|escape: html|default:$smarty.session.r_nachricht}</textarea></td>
        </tr>
      </table>
      <br />
      {if $smarty.session.ship_ok == 1}
        <a name="diff_adress"></a>
        <div class="shop_data_forms_headers">{#Shop_f_shippingadress#}</div>
        <label><input onclick="document.getElementById('liefer_andere_div').style.display = 'none';" type="radio" name="diff_rl" value="liefer_gleich" {if $smarty.request.diff_rl == 'liefer_gleich' || empty($smarty.request.diff_rl) || $smarty.session.diff_rl == 'liefer_gleich'}checked="checked"{/if} /> {#Shop_f_same_sa#} </label>
        <br />
        <label><input onclick="document.getElementById('liefer_andere_div').style.display = '';" type="radio" name="diff_rl" value="liefer_andere" {if $smarty.request.diff_rl == 'liefer_andere' || $smarty.session.diff_rl == 'liefer_andere'}checked="checked"{/if} /> {#Shop_f_other_sa#} </label>
        <br />
        <br />
        <div id="liefer_andere_div" {if $smarty.request.diff_rl == 'liefer_andere' || $smarty.session.diff_rl == 'liefer_andere'}{else}style="display: none"{/if}>
          <table width="100%" cellpadding="2" cellspacing="0" class="box_inner">
            {if $smarty.session.ship_ok == 1 || $settings.Reg_DataPflicht == 1}
              <tr>
                <td width="200"><label for="l_l_nachname">{#LastName#}</label>&nbsp;</td>
                <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_DataPflichtFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
                <td nowrap="nowrap"><input class="input" style="width: 200px" name="l_nachname" type="text" id="l_l_nachname" value="{$smarty.post.l_nachname|escape: html|default:$smarty.session.l_nachname}" /></td>
              </tr>
              <tr>
                <td width="200"><label for="l_l_vorname">{#GlobalName#}</label>&nbsp;</td>
                <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_DataPflichtFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
                <td nowrap="nowrap"><input class="input" style="width: 200px" name="l_vorname" type="text" id="l_l_vorname" value="{$smarty.post.l_vorname|escape: html|default:$smarty.session.l_vorname}" /></td>
              </tr>
              <tr>
                <td width="200"><label for="l_l_middlename">{#Profile_MiddleName#}</label>&nbsp;</td>
                <td width="5">&nbsp;</td>
                <td nowrap="nowrap"><input class="input" style="width: 200px" name="l_middlename" type="text" id="l_l_middlename" value="{$smarty.post.l_middlename|escape: html|default:$smarty.session.l_middlename}" /></td>
              </tr>
            {/if}
            {if $settings.Reg_Fon == 1 || $shopsettings->Telefon_Pflicht}
              <tr>
                <td width="200"><label for="l_l_telefon">{#Phone#}</label>&nbsp;</td>
                <td width="5">{if $shopsettings->Telefon_Pflicht}<sup>*</sup>{/if}</td>
                <td nowrap="nowrap"><input class="input" style="width: 200px" name="l_telefon" type="text" id="l_l_telefon" value="{$smarty.post.l_telefon|escape: html|default:$smarty.session.l_telefon}" /></td>
              </tr>
            {/if}
            {if $settings.Reg_Fax == 1}
              <tr>
                <td width="200"><label for="l_l_fax">{#Fax#}</label>&nbsp;</td>
                <td width="5">&nbsp;</td>
                <td nowrap="nowrap"><input class="input" style="width: 200px" name="l_fax" type="text" id="l_l_fax" value="{$smarty.post.l_fax|escape: html|default:$smarty.session.l_fax}" /></td>
              </tr>
            {/if}
            {if $settings.Reg_Firma == 1 && $smarty.session.type_client != 1}
              <tr>
                <td width="200"><label for="l_l_firma">{#Profile_company#}</label>&nbsp;</td>
                <td width="5">&nbsp;</td>
                <td nowrap="nowrap"><input class="input" style="width: 200px" name="l_firma" type="text" id="l_l_firma" value="{$smarty.post.l_firma|escape: html|default:$smarty.session.l_firma}" /></td>
              </tr>
            {/if}
            <tr>
              <td width="200"><label for="l_l_land">{#Country#}</label>&nbsp;</td>
              <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
              <td nowrap="nowrap">
                <select class="input" name="l_land" id="l_l_land" style="width: 210px">
                  {foreach from=$countries item=c}
                    <option value="{$c.Code}" {if $smarty.session.l_land == $c.Code}selected="selected"{else}{if $smarty.request.save != '1' && $shop_country == $c.Code && !$smarty.session.l_land}selected="selected"{elseif $smarty.request.l_land == $c.Code}selected="selected"{/if}{/if}>
                      {$c.Name}
                    </option>
                  {/foreach}
                </select>
              </td>
            </tr>
            {if $smarty.session.ship_ok == 1 || $settings.Reg_Address == 1}
              <tr>
                <td width="200"><label for="l_l_plz">{#Profile_Zip#}</label>&nbsp;</td>
                <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
                <td nowrap="nowrap"><input class="input" style="width: 200px" name="l_plz" type="text" id="l_l_plz" value="{$smarty.post.l_plz|escape: html|default:$smarty.session.l_plz}" /></td>
              </tr>
              <tr>
                <td width="200"><label for="l_l_ort">{#Town#}</label>&nbsp;</td>
                <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
                <td nowrap="nowrap"><input class="input" style="width: 200px" name="l_ort" type="text" id="l_l_ort" value="{$smarty.post.l_ort|escape: html|default:$smarty.session.l_ort}" /></td>
              </tr>
              <tr>
                <td width="200"><label for="l_l_strasse">{#Profile_Street#}</label>&nbsp;</td>
                <td width="5">{if $smarty.session.ship_ok == 1 || $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
                <td nowrap="nowrap"><input class="input" style="width: 200px" name="l_strasse" type="text" id="l_l_strasse" value="{$smarty.post.l_strasse|escape: html|default:$smarty.session.l_strasse}" /></td>
              </tr>
            {/if}
          </table>
        </div>
        <br />
      {/if}
      <div class="shop_next_step">
        <input type="hidden" name="p" value="shop" />
        <input type="hidden" name="area" value="{$area}" />
        <input type="hidden" name="action" value="shoporder" />
        <input type="hidden" name="subaction" value="step2" />
        {if $guest_order == 1}
          <input type="hidden" name="order" value="guest" />
        {/if}
        <input type="hidden" name="save" value="1" />
        <input type="submit" class="button" value="{#Shop_nextStep#}" />
      </div>
    </form>
  </div>
{/if}
<br />
