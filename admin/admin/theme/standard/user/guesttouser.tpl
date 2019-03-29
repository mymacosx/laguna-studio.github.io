<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#convert').validate({
        rules: {
            Vorname: { required: true },
            Nachname: { required: true },
            Benutzername: { required: true, remote: "index.php?do=user&sub=checkuserdata&ext={$res->Benutzername|sanitize}" },
            Kennwort: { required: true, minlength: 5 },
            Email: { required: true, email: true, remote: "index.php?do=user&sub=checkuserdata&ext={$res->Email}" }
        },
        messages: {
            Vorname: { required: '' },
            Nachname: { required: '' },
            Kennwort: { required: '' },
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

<form method="post" action="" id="convert">
  <div class="popbox">
    <div class="subheaders">{#User_convertGuestToUser#}</div>
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td width="200" class="row_left">{#User_last#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Nachname" value="{$res->Rng_Nachname}" /></td>
        <td width="200" class="row_left">{#User_street#}</td>
        <td class="row_right"><input class="input" type="text" name="Strasse_Nr" value="{$res->Rng_Strasse}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_first#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Vorname" value="{$res->Rng_Vorname}" /></td>
        <td width="200" class="row_left">{#User_zip#}</td>
        <td class="row_right"><input class="input" type="text" name="Postleitzahl" value="{$res->Rng_Plz}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_MiddleName#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="MiddleName" value="{$res->Rng_MiddleName|sanitize}" /></td>
        <td width="200" class="row_left">{#User_town#}</td>
        <td class="row_right"><input class="input" type="text" name="Ort" value="{$res->Rng_Ort}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_company#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Firma" value="{$res->Rng_Firma|sanitize}" /></td>
        <td width="200" class="row_left">{#User_country#}</td>
        <td class="row_right">
          <select class="input" style="width: 130px" name="Land">
            {foreach from=$countries item=c}
              <option value="{$c.Code|lower}" {if $c.Code|lower == $country_short|lower}selected="selected"{/if}>{$c.Name|sanitize}</option>
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_ustid#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="UStId" value="{$res->UStId|sanitize}" /></td>
        <td width="200" class="row_left"></td>
        <td class="row_right"></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_Bank#}</td>
        <td colspan="3" class="row_right"><textarea cols="" rows="" name="BankName" id="BankName" class="input" style="width: 350px; height: 70px">{$res->Rng_BankName|sanitize}</textarea></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#Global_Email#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Email" value="{$res->Rng_Email}" /></td>
        <td width="200" class="row_left">{#User_phone#}</td>
        <td class="row_right"><input class="input" type="text" name="Telefon" value="{$res->Rng_Fon}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_username#}</td>
        <td width="200" class="row_right"><input class="input" type="text" name="Benutzername" value="{$res->Rng_Vorname} {$res->Rng_Nachname|truncate: 2: '.'}" /></td>
        <td width="200" class="row_left">{#User_fax#}</td>
        <td class="row_right"><input class="input" type="text" name="Telefax" value="{$res->Rng_Fax}" /></td>
      </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td width="200" class="row_left">{#Groups_Name#}</td>
        <td class="row_right">
          <select class="input" style="width: 130px" name="Gruppe">
            {foreach from=$groups item=g}
              {if $g->Id != 2}
                <option value="{$g->Id}" {if $g->Id == 3}selected="selected"{/if}>{$g->Name_Intern|sanitize}</option>
              {/if}
            {/foreach}
          </select>
        </td>
      </tr>
      <tr>
        <td width="200" class="row_left"><img class="absmiddle stip" title="{$lang.User_markteamI|sanitize}" src="{$imgpath}/help.png" alt="" /> {#User_team#}</td>
        <td class="row_right">
          <label><input type="radio" name="team" value="1" />{#Yes#}</label>
          <label><input type="radio" name="team" value="0" checked="checked" />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#LoginPass#}</td>
        <td class="row_right"><input class="input" type="text" name="Kennwort" value="{$password}" /></td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_semail#}</td>
        <td class="row_right">
          <label><input name="send_mail" type="radio" id="send_mail" value="1" checked="checked" />{#Yes#}</label>
          <label><input type="radio" name="send_mail" value="0" />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td width="200" class="row_left">{#User_emailtext#}</td>
        <td class="row_right"><textarea cols="" rows="" name="mail_text" id="mail_text" class="input" style="width: 500px; height: 100px">{#Shop_convertGuestMail#}</textarea></td>
      </tr>
    </table>
    <input class="button" type="submit" name="button" value="{#Save#}" />
    <input class="button" type="button" name="button" onclick="closeWindow(true);" value="{#Close#}" />
    <input name="save" type="hidden" id="save" value="1" />
  </div>
</form>
