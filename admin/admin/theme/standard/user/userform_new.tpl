<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#Geburtstag').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });

    $('#convert').validate( {
        rules: {
            {if $settings.Reg_DataPflichtFill == 1}
            Vorname: { required: true, minlength: 3 },
            Nachname: { required: true,minlength: 3 },
            {/if}
            Benutzername: { required: true, remote: "index.php?do=user&sub=checkuserdata&ext={$smarty.request.Benutzername|sanitize}" },
            {if $settings.Reg_AddressFill == 1}
            Strasse_Nr: { required: true },
            Postleitzahl: { required: true },
            Ort: { required: true },
            {/if}
            Email: { required: true, email: true, remote: "index.php?do=user&sub=checkuserdata&ext={$smarty.request.Email}" },
            Kennwort: { required: true }
        },
        messages: {
            Email: { remote: $.validator.format("{#Validate_usedMail#}") },
            Benutzername: { remote: $.validator.format("{#Validate_usedUName#}") }
        },
        submitHandler: function() {
            document.forms['convert'].submit();
        },
        success: function(label) {
            label.html("&nbsp;").addClass("checked");
        }
    });
});
//-->
</script>

<form autocomplete="off" method="post" action="" name="convert" id="convert">
  {if $done == 1}
    <div class="subheaders">
      {#User_profileNewOk#}
      <p align="center"><input class="button" type="button" name="button" onclick="closeWindow();" value="{#Close#}" /></p>
    </div>
  {else}
    {if $error}
      <div class="error_box"><strong>{#Global_Error#}</strong>
        <ul>
          {foreach from=$error item=e}
            <li>{$e}</li>
            {/foreach}
        </ul>
      </div>
    {/if}
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td width="200" class="row_left">{#User_last#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Nachname" value="{$smarty.request.Nachname|sanitize}" /></td>
        <td width="200" class="row_left">{#User_street#}</td>
        <td class="row_right"><input class="input" type="text" name="Strasse_Nr" value="{$smarty.request.Strasse_Nr|sanitize}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_first#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Vorname" value="{$smarty.request.Vorname|sanitize}" /></td>
        <td width="200" class="row_left">{#User_zip#}</td>
        <td class="row_right"><input class="input" type="text" name="Postleitzahl" value="{$smarty.request.Postleitzahl|sanitize}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_MiddleName#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="MiddleName" value="{$smarty.request.MiddleName|sanitize}" /></td>
        <td width="200" class="row_left">{#User_town#}</td>
        <td class="row_right"><input class="input" type="text" name="Ort" value="{$smarty.request.Ort|sanitize}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_company#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Firma" value="{$smarty.request.Firma|sanitize}" /></td>
        <td width="200" class="row_left">{#User_country#}</td>
        <td class="row_right">
          <select class="input" style="width: 130px" name="Land">
            {foreach from=$countries item=c}
              <option value="{$c.Code|lower}" {if $c.Code|lower == $settings.Land|lower}selected="selected"{/if}>{$c.Name|sanitize}</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_ustid#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="UStId" value="{$smarty.request.UStId|sanitize}" /></td>
        <td width="200" class="row_left">{#Comments_web#}</td>
        <td class="row_right"><input class="input" type="text" name="Webseite" value="{$smarty.request.Webseite|sanitize}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_Bank#}</td>
        <td colspan="3" class="row_right"><textarea cols="" rows="" name="BankName" id="BankName" class="input" style="width: 350px; height: 70px">{$smarty.request.BankName|sanitize}</textarea></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#Global_Email#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Email" value="{$smarty.request.Email}" /></td>
        <td width="200" class="row_left">{#User_phone#}</td>
        <td class="row_right"><input class="input" type="text" name="Telefon" value="{$smarty.request.Telefon|sanitize}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_username#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Benutzername" value="{$smarty.request.Benutzername|sanitize}" /></td>
        <td width="200" class="row_left">{#User_fax#}</td>
        <td class="row_right"><input class="input" type="text" name="Telefax" value="{$smarty.request.Telefax|sanitize}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#Geburtstag#} - ??.??.????</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Geburtstag" id="Geburtstag" value="{$smarty.request.Geburtstag}" readonly="readonly" /></td>
        <td width="200" class="row_left"><img class="absmiddle stip" title="{$lang.FSK18_Userinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#FSK18_User#}</td>
        <td class="row_right">
          <label><input name="Fsk18" type="radio" value="1" />{#Yes#}</label>
          <label><input name="Fsk18" type="radio" value="0" checked="checked" />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#Groups_Name#}</td>
        <td width="200" class="row_right">
          <select class="input" style="width: 130px" name="Gruppe">
            {foreach from=$groups item=g}
              {if $g->Id != 2}
                <option value="{$g->Id}" {if $g->Id == 3}selected="selected"{/if}>{$g->Name_Intern|sanitize}</option>
              {/if}
            {/foreach}
          </select>
        </td>
        <td width="200" class="row_left"><img class="absmiddle stip" title="{$lang.User_markteamI|sanitize}" src="{$imgpath}/help.png" alt="" /> {#User_team#}</td>
        <td class="row_right">
          <label><input type="radio" name="Team" value="1" />{#Yes#}</label>
          <label><input type="radio" name="Team" value="0" checked="checked" />{#No#}</label>
        </td>
      </tr>
    </table>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td width="200" class="row_left">{#User_nopassmail#}</td>
        <td class="row_right">
          <label>
            <input type="radio" name="Geloescht" value="1" />{#Yes#}
            <input type="radio" name="Geloescht" value="0" checked="checked" />{#No#}
          </label>
        </td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_sstatus#}</td>
        <td class="row_right">
          <label>
            <input type="radio" name="Aktiv" value="1" checked="checked" />{#Global_Active#}
            <input type="radio" name="Aktiv" value="0" />{#Global_Inactive#}
          </label>
        </td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#LoginPass#}</td>
        <td class="row_right"><input class="input" type="text" name="Kennwort" value="{$smarty.request.Kennwort|sanitize|default:$password}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_semail#}</td>
        <td class="row_right">
          <label><input name="send_mail" type="radio" id="send_mail" value="1" checked="checked" />{#Yes#}</label>
          <label><input name="send_mail" type="radio" value="0" />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_emailtext#}</td>
        <td class="row_right"><textarea cols="" rows="" name="mail_text" id="mail_text" class="input" style="width: 400px; height: 150px">{$lang.User_profileNewText|replace: "__N__": "\n"}</textarea></td>
      </tr>
    </table>
    <input class="button" type="submit" name="button" value="{#Save#}" />
    <input class="button" type="button" name="button" onclick="closeWindow();" value="{#Close#}" />
    <input name="save" type="hidden" id="save" value="1" />
  {/if}
</form>
