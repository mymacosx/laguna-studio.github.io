{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    var validator = $('#regform').validate( {
        rules: {
            {if $settings.Reg_Pass == 1}
            reg_pass: { required: true, minlength: 6},
            reg_pass2: { required: true, equalTo: '#l_reg_pass' },
            {/if}
            {if $settings.Reg_AgbPflicht == 1}
            agb_checked: { required: true },
            {/if}
            {if $settings.Reg_DataPflichtFill == 1}
            Vorname: { required: true },
            Nachname: { required: true },
            {/if}
            {if $settings.Reg_AddressFill == 1}
            Strasse_Nr: { required: true },
            Postleitzahl: { required: true, number: true, minlength: 4 },
            Ort: { required: true },
            {/if}
            reg_email: { required: true, email: true, remote: '{$baseurl}/index.php?do=checkuserdata&p=register' },
            reg_email2: { required: true, equalTo: '#l_reg_email' },
            reg_username: { required: true, remote: '{$baseurl}/index.php?do=checkuserdata&p=register' }
        },
        messages: {
            {if $settings.Reg_AgbPflicht == 1}
            agb_checked: { required: '{#Reg_agb_failed#}' },
            {/if}
            reg_email: { remote: $.validator.format('{#Validate_usedMail#}') },
            reg_username: { remote: $.validator.format('{#Validate_usedUName#}') }
        },
        submitHandler: function() {
            document.forms['regform'].submit();
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
});
// -->
</script>

{if $shop == 1}
  {include file="$incpath/shop/shop_steps.tpl"}
{/if}
{if !empty($error)}
  <div class="error_box">
    {#Reg_Failed#}
    <ul>
      {foreach from=$error item=err}
        <li>{$err}</li>
        {/foreach}
    </ul>
  </div>
{/if}
<form autocomplete="off" method="post" name="regform" id="regform" action="">
  <div class="box_innerhead"> {#Reg_LoginData#}</div>
  <div class="box_data">
    <table width="100%" cellpadding="0" cellspacing="0" class="box_inner">
      <tr>
        <td width="180" nowrap="nowrap" class="row_first" align="right"><label for="l_reg_email">{#Email#}&nbsp;</label></td>
        <td width="5"><sup>*</sup></td>
        <td nowrap="nowrap" class="row_second"><input type="text" name="reg_email" id="l_reg_email" value="{$smarty.post.reg_email|sanitize}" class="input" style="width: 140px" /></td>
      </tr>
      <tr>
        <td nowrap="nowrap" class="row_first" align="right"><label for="l_reg_email_2">{#Reg_Email2#}&nbsp;</label></td>
        <td width="5"><sup>*</sup></td>
        <td nowrap="nowrap" class="row_second"><input type="text" name="reg_email2" id="l_reg_email_2" value="{$smarty.post.reg_email2|sanitize}" class="input" style="width: 140px" /></td>
      </tr>
      <tr>
        <td nowrap="nowrap" class="row_first" align="right"><label for="l_reg_username">{#Reg_Username#}&nbsp;</label></td>
        <td width="5"><sup>*</sup></td>
        <td nowrap="nowrap" class="row_second"><input type="text" name="reg_username" id="l_reg_username" class="input" style="width: 140px" value="{$smarty.post.reg_username|sanitize}" /></td>
      </tr>
      {if $settings.Reg_Pass == 1}
        <tr>
          <td width="180" nowrap="nowrap" class="row_first" align="right"><label for="l_reg_pass">{#Pass#}&nbsp;</label></td>
          <td width="5"><sup>*</sup></td>
          <td nowrap="nowrap" class="row_second"><input name="reg_pass" type="password" class="input" id="l_reg_pass" style="width: 140px" value="{$smarty.post.reg_pass|sanitize}" /></td>
        </tr>
        <tr>
          <td nowrap="nowrap" class="row_first" align="right"><label for="l_reg_pass_2">{#Reg_Pass2#}&nbsp;</label></td>
          <td width="5"><sup>*</sup></td>
          <td nowrap="nowrap" class="row_second"><input name="reg_pass2" type="password" class="input" id="l_reg_pass_2" style="width: 140px" value="{$smarty.post.reg_pass2|sanitize}" /></td>
        </tr>
      {/if}
    </table>
  </div>
  {if $settings.Reg_DataPflicht == 1}
    <div class="box_innerhead">{#PersonalData#}</div>
    <div class="box_data">
      <table width="100%" cellpadding="0" cellspacing="0" class="box_inner">
        {if $settings.Reg_DataPflicht == 1}
          <tr>
            <td nowrap="nowrap" class="row_first" align="right"><label for="l_nachname">{#LastName#}&nbsp;</label></td>
            <td width="5">{if $settings.Reg_DataPflichtFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
            <td nowrap="nowrap" class="row_second"><input class="input" style="width: 160px" name="Nachname" type="text" id="l_nachname" value="{$smarty.post.Nachname|default:$data.Nachname|sanitize}" /></td>
          </tr>
          <tr>
            <td nowrap="nowrap" class="row_first" align="right"><label for="l_vorname">{#GlobalName#}&nbsp;</label></td>
            <td width="5">{if $settings.Reg_DataPflichtFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
            <td nowrap="nowrap" class="row_second"><input class="input" style="width: 160px" name="Vorname" type="text" id="l_vorname" value="{$smarty.post.Vorname|default:$data.Vorname|sanitize}" /></td>
          </tr>
          <tr>
            <td nowrap="nowrap" class="row_first" align="right"><label for="l_middlename">{#Profile_MiddleName#}&nbsp;</label></td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap" class="row_second"><input class="input" style="width: 160px" name="MiddleName" type="text" id="l_middlename" value="{$smarty.post.MiddleName|default:$data.MiddleName|sanitize}" /></td>
          </tr>
        {/if}
        {if $settings.Reg_Birth == 1}
          <tr>
            <td width="180" nowrap="nowrap" class="row_first" align="right"><label for="l_reg_birth">{#Birth#}&nbsp;</label></td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap" class="row_second"><input class="input" name="birth" type="text" id="l_reg_birth" style="width: 160px" value="{$smarty.post.birth|sanitize}" maxlength="10" />&nbsp;&nbsp;{#BirthFormat#} </td>
          </tr>
        {/if}
        {if $settings.Reg_Fon == 1}
          <tr>
            <td nowrap="nowrap" class="row_first" align="right"><label for="l_fon">{#Phone#}&nbsp;</label></td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap" class="row_second"><input class="input" style="width: 160px" name="Telefon" type="text" id="l_fon" value="{$smarty.post.Telefon|default:$data.Telefon|sanitize}" /></td>
          </tr>
        {/if}
        {if $settings.Reg_Fax == 1}
          <tr>
            <td nowrap="nowrap" class="row_first" align="right"><label for="l_fax">{#Fax#}&nbsp;</label></td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap" class="row_second"><input class="input" style="width: 160px" name="Telefax" type="text" id="l_fax" value="{$smarty.post.Telefax|default:$data.Telefax|sanitize}" /></td>
          </tr>
        {/if}
        <tr>
          <td width="180" nowrap="nowrap" class="row_first" align="right"><label for="l_reg_country">{#Country#}&nbsp;</label></td>
          <td width="5">&nbsp;</td>
          <td nowrap="nowrap" class="row_second"><select class="input" name="country" id="l_reg_country" style="width: 170px">
              {foreach from=$countries item=c}
                <option value="{$c.Code}" {if $smarty.request.send != 1}{if $settings.Land|upper == $c.Code}selected="selected"{/if}{else}{if $smarty.post.country == $c.Code}selected="selected"{/if}{/if}>{$c.Name}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        {if $settings.Reg_Address == 1}
          <tr>
            <td nowrap="nowrap" class="row_first" align="right"><label for="l_zip">{#Profile_Zip#}&nbsp;</label></td>
            <td width="5">{if $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
            <td nowrap="nowrap" class="row_second"><input class="input" style="width: 160px" name="Postleitzahl" type="text" id="l_zip" value="{$smarty.post.Postleitzahl|default:$data.Postleitzahl|sanitize}" /></td>
          </tr>
          <tr>
            <td nowrap="nowrap" class="row_first" align="right"><label for="l_ort">{#Town#}&nbsp;</label></td>
            <td width="5">{if $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
            <td nowrap="nowrap" class="row_second"><input class="input" style="width: 160px" name="Ort" type="text" id="l_ort" value="{$smarty.post.Ort|default:$data.Ort|sanitize}" /></td>
          </tr>
          <tr>
            <td nowrap="nowrap" class="row_first" align="right"><label for="l_strassenr">{#Profile_Street#}&nbsp;</label></td>
            <td width="5">{if $settings.Reg_AddressFill == 1}<sup>*</sup>{else}&nbsp;{/if}</td>
            <td nowrap="nowrap" class="row_second"><input class="input" style="width: 160px" name="Strasse_Nr" type="text" id="l_strassenr" value="{$smarty.post.Strasse_Nr|default:$data.Strasse_Nr|sanitize}" /></td>
          </tr>
        {/if}
        {if $settings.Reg_Firma == 1}
          <tr>
            <td nowrap="nowrap" class="row_first" align="right"><label for="l_firma">{#Profile_company#}&nbsp;</label></td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap" class="row_second"><input class="input" style="width: 160px" name="Firma" type="text" id="l_firma" value="{$smarty.post.Firma|default:$data.Firma|sanitize}" /></td>
          </tr>
        {/if}
        {if $settings.Reg_Ust == 1}
          <tr>
            <td nowrap="nowrap" class="row_first" align="right"><label for="l_ustid">{#Profile_vatnum#}&nbsp;</label></td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap" class="row_second"><input class="input" style="width: 160px" name="UStId" type="text" id="l_ustid" value="{$smarty.post.UStId|default:$data.ustid|sanitize}" /></td>
          </tr>
        {/if}
        {if $settings.Reg_Bank == 1}
          <tr>
            <td width="180" nowrap="nowrap" class="row_first" align="right"><label for="l_reg_bank">{#Profile_Bank#}</label>&nbsp;</td>
            <td width="5">&nbsp;</td>
            <td nowrap="nowrap" class="row_second"><textarea name="BankName" cols="" rows="" class="input" id="l_reg_bank" style="width: 300px; height: 150px">{$smarty.post.BankName|sanitize}</textarea></td>
          </tr>
        {/if}
        <tr>
          <td class="row_first">&nbsp;</td>
          <td width="5">&nbsp;</td>
          <td nowrap="nowrap" class="row_second">{#Profile_RequiredInf#}</td>
        </tr>
      </table>
    </div>
  {else}
    <input type="hidden" name="country" value="{$settings.Land}" />
  {/if}
  {if $settings.Reg_AgbPflicht == 1}

<script type="text/javascript">
<!-- //
function agbOut() {
    var tag = 'body';
    var print = document.getElementById('sagb').innerHTML;
    print = print.replace(/src="/gi, 'src="../');
    print = print.replace(/&lt;/gi, '<');
    print = print.replace(/&gt;/gi, '>');
    var win = window.open('', null, 'height=600,width=780,toolbar=yes,location=no,status=yes,menubar=no,scrollbars=yes,resizable=no');
    var out = '<html><' + tag + ' style="font-family: arial,verdana;font-size: 12px" onload="window.print()">' + print + '</' + tag + '></html>';
    win.document.write(out);
    win.document.close();
}
//-->
</script>

    <div class="box_innerhead">{#Reg_agb#}</div>
    <div class="box_data padding5">
      <div id="sagb" style="height: 150px; overflow: auto; border: 1px solid gray; padding: 3px" class="reg_agb">{$settings.Reg_Agb}</div>
      <br />
      <label><input type="checkbox" name="agb_checked" value="1" />&nbsp;<strong>{#Reg_agb_inf#}</strong></label>
      | <a href="" onclick="agbOut(); return false;"><strong>{#Print#}</strong></a>
    </div>
  {/if}
  {include file="$incpath/other/captcha.tpl"}
  <p class="reg_buttons">
    <input type="submit" value="{#RegNew#}" class="button" />&nbsp;
    <input type="reset" class="button" />
    <input name="area" type="hidden" value="{$area}" />
    <input name="lang" type="hidden" value="{$langcode}" />
    <input type="hidden" name="p" value="register" />
    <input type="hidden" name="send" value="1" />
    {if $shop == 1}
      <input type="hidden" name="p" value="shop" />
      <input type="hidden" name="action" value="shoporder" />
      <input type="hidden" name="subaction" value="step2" />
      <input type="hidden" name="register" value="new" />
      <input type="hidden" name="mode" value="shop" />
    {/if}
  </p>
</form>
