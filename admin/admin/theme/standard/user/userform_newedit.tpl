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
            Nachname: { required: true, minlength: 3 },
            {/if}
            Benutzername: { required: true, remote: "index.php?do=user&sub=checkuserdata&ext={$res->Benutzername|sanitize}" },
            {if $settings.Reg_AddressFill == 1}
            Strasse_Nr: { required: true },
            Postleitzahl: { required: true },
            Ort: { required: true },
           {/if}
            Email: { required: true, email: true, remote: "index.php?do=user&sub=checkuserdata&ext={$res->Email}" }
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


<form method="post" action="" name="convert" id="convert">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="200" class="row_left">{#User_last#}</td>
      <td width="200" class="row_right"><input class="input" type="text" name="Nachname" value="{$res->Nachname|sanitize}" /></td>
      <td width="200" class="row_left">{#User_street#}</td>
      <td class="row_right"><input class="input" type="text" name="Strasse_Nr" value="{$res->Strasse_Nr|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#User_first#}</td>
      <td width="200" class="row_right"><input class="input" type="text" name="Vorname" value="{$res->Vorname|sanitize}" /></td>
      <td width="200" class="row_left">{#User_zip#}</td>
      <td class="row_right"><input class="input" type="text" name="Postleitzahl" value="{$res->Postleitzahl|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#User_MiddleName#}</td>
      <td width="200" class="row_right"><input class="input" type="text" name="MiddleName" value="{$res->MiddleName|sanitize}" /></td>
      <td width="200" class="row_left">{#User_town#}</td>
      <td class="row_right"><input class="input" type="text" name="Ort" value="{$res->Ort|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#User_company#}</td>
      <td width="200" class="row_right"><input class="input" type="text" name="Firma" value="{$res->Firma|sanitize}" /></td>
      <td width="200" class="row_left">{#User_country#}</td>
      <td class="row_right">
        <select class="input" style="width: 130px" name="Land">
          {foreach from=$countries item=c}
            <option value="{$c.Code|lower}" {if $c.Code|lower == $res->LandCode|lower}selected="selected"{/if}>{$c.Name|sanitize}</option>
          {/foreach}
        </select>
      </td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#User_ustid#}</td>
      <td width="200" class="row_right"><input class="input" type="text" name="UStId" value="{$res->UStId|sanitize}" /></td>
      <td width="200" class="row_left">{#Comments_web#}</td>
      <td class="row_right"><input class="input" type="text" name="Webseite" value="{$res->Webseite|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#User_Bank#}</td>
      <td colspan="3" class="row_right"><textarea cols="" rows="" name="BankName" id="BankName" class="input" style="width: 350px; height: 70px">{$res->BankName|sanitize}</textarea></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#Global_Email#}</td>
      <td width="200" class="row_right"><input class="input" type="text" name="Email" value="{$res->Email}" /></td>
      <td width="200" class="row_left">{#User_phone#}</td>
      <td class="row_right"><input class="input" type="text" name="Telefon" value="{$res->Telefon|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#User_username#}</td>
      <td width="200" class="row_right"><input class="input" type="text" name="Benutzername" value="{$res->Benutzername|sanitize}" /></td>
      <td width="200" class="row_left">{#User_fax#}</td>
      <td class="row_right"><input class="input" type="text" name="Telefax" value="{$res->Telefax|sanitize}" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#Geburtstag#} - ??.??.????</td>
      <td width="200" class="row_right"><input class="input" type="text" name="Geburtstag" id="Geburtstag" value="{$res->Geburtstag}" /></td>
      <td width="200" class="row_left"><img class="absmiddle stip" title="{$lang.FSK18_Userinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#FSK18_User#}</td>
      <td class="row_right">
        <label><input name="Fsk18" type="radio" value="1" {if $res->Fsk18 == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input name="Fsk18" type="radio" value="0" {if $res->Fsk18 == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#Groups_Name#}</td>
      <td width="200" class="row_right">
        {if $smarty.session.benutzer_id!=$res->Id && $res->Id != 1}
          <select class="input" style="width: 130px" name="Gruppe">
            {foreach from=$groups item=g}
              {if $g->Id != 2}
                <option value="{$g->Id}" {if $g->Id == $res->Gruppe}selected="selected"{/if}>{$g->Name_Intern|sanitize}</option>
              {/if}
            {/foreach}
          </select>
        {else}
          <input type="hidden" name="Gruppe" value="{$res->Gruppe}" />
          <em>{#User_noChange#}</em>
        {/if}
      </td>
      <td width="200" class="row_left"><img class="absmiddle stip" title="{$lang.User_markteamI|sanitize}" src="{$imgpath}/help.png" alt="" /> {#User_team#}</td>
      <td class="row_right">
        <label><input type="radio" name="Team" value="1" {if $res->Team == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="Team" value="0" {if $res->Team == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
  </table>
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="200" class="row_left">{#User_nopassmail#}</td>
      <td class="row_right">
        <label><input type="radio" name="Geloescht" value="1" {if $res->Geloescht == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="Geloescht" value="0" {if $res->Geloescht == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    {if $res->Id != 1}
      <tr>
        <td width="200" class="row_left">{#User_sstatus#}</td>
        <td class="row_right">
          <label><input type="radio" name="Aktiv" value="1" {if $res->Aktiv == 1}checked="checked"{/if} />{#Global_Active#}</label>
          <label><input type="radio" name="Aktiv" value="0" {if $res->Aktiv == 0}checked="checked"{/if} />{#Global_Inactive#}</label>
        </td>
      </tr>
    {/if}
    <tr>
      <td width="200" class="row_left">{#User_newPass#}</td>
      <td class="row_right"><input class="input" type="text" name="Kennwort" /></td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#User_semail#}</td>
      <td class="row_right">
        <label><input name="send_mail" type="radio" id="send_mail" value="1" />{#Yes#}</label>
        <label><input name="send_mail" type="radio" value="0" checked="checked" />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="200" class="row_left">{#User_emailtext#}</td>
      <td class="row_right"><textarea cols="" rows="" name="mail_text" id="mail_text" class="input" style="width: 500px; height: 120px">{$lang.User_profileChangeText|replace: "__N__": "\n"}</textarea></td>
    </tr>
  </table>
  <input class="button" type="submit" name="button" value="{#Save#}" />
  <input class="button" type="button" name="button" onclick="closeWindow(true);" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
