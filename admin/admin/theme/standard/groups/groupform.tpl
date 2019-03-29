<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
	document.forms['editform'].submit();
    }
});
$(document).ready(function() {
    $('#editform').validate({
        rules: {
	    Name: { required: true },
	    Name_Intern: { required: true },
	    Rabatt: { required: true, range: [0,99] },
	    MaxPn: { required: true, range: [0,500] },
	    MaxPn_Zeichen: { required: true, range: [250,5000] },
	    Signatur_Laenge: { required: true, range: [50,500] },
	    MaxZeichenPost: { required: true, range: [500,15000] },
	    MaxAnlagen: { required: true, range: [0,10] },
	    Avatar_B: { required: true, range: [50,200] },
	    Avatar_H: { required: true, range: [50,200] }
	},
	messages: { }
    });
});
function fileUpload(sub, divid) {
    var width = document.getElementById('Avatar_B').value;
    if(width < 10 || width > 200) {
	alert('{#Groups_AvatarError#}');
	document.getElementById('Avatar_B').focus();
	return false;
    }
    $(document).ajaxStart(function() {
        $('#loading_' + divid).show();
        $('#buttonUpload_' + divid).val('{#Global_Wait#}').prop('disabled', true);
    }).ajaxComplete(function() {
        $('#loading_' + divid).hide();
        $('#buttonUpload_' + divid).val('{#UploadButton#}').prop('disabled', false);
    });
    $.ajaxFileUpload({
	url: 'index.php?do=groups&sub=' + sub + '&divid=' + divid + '&resize=' + width,
	secureuri: false,
	fileElementId: 'fileToUpload_' + divid,
	dataType: 'json',
	success: function (data) {
	    if(typeof(data.result) !== 'undefined') {
                document.getElementById('UpInf_' + divid).innerHTML = data.result;
                if(data.filename !== '') {
                    document.getElementById('newFile_' + divid).value = data.filename;
                }
	    }
        },
        error: function (data, status, e) {
	    document.getElementById('UpInf_' + divid).innerHTML = e;
	}
    });
    return false;
}
//-->
</script>

<form action="" method="post" name="editform" id="editform">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td width="300" class="row_left">{#Global_Name#}</td>
      <td class="row_right"><input class="input" style="width: 150px" name="Name" type="text" id="Name" value="{$res->Name|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Groups_NameIntern#}</td>
      <td class="row_right"><input class="input" style="width: 150px" name="Name_Intern" type="text" value="{$res->Name_Intern|sanitize}" /></td>
    </tr>
    <tr>
      <td colspan="2" class="headers_row">{#Groups_ShopSettings#}</td>
    </tr>
    <tr>
      <td class="row_left"><img class="absmiddle stip" title="{$lang.Groups_ShopDedInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Groups_ShopDed#}</td>
      <td class="row_right"><input class="input" name="Rabatt" type="text" value="{$res->Rabatt}" size="4" maxlength="5" /> %</td>
    </tr>
    <tr>
      <td class="row_left"><img class="absmiddle stip" title="{$lang.Groups_ShopDispInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Groups_ShopDisp#}</td>
      <td class="row_right">
        <select class="input" name="ShopAnzeige">
          <option value="b2c" {if $res->ShopAnzeige == 'b2c'}selected="selected"{/if}>{#B2C_Cena#}</option>
          <option value="b2b" {if $res->ShopAnzeige == 'b2b'}selected="selected"{/if}>{#B2B_Cena#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="row_left"><img class="absmiddle stip" title="{$lang.Groups_TakeVatInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Groups_TakeVat#}</td>
      <td class="row_right"><select class="input" name="VatByCountry">
          <option value="1" {if $res->VatByCountry == 1}selected="selected"{/if}>{#Yes#} ({#Global_Std#})</option>
          <option value="2" {if $res->VatByCountry == 2}selected="selected"{/if}>{#No#}</option>
        </select></td>
    </tr>
    {if $smarty.request.id != 2}
      <tr>
        <td colspan="2" class="headers_row">{#Groups_PN#}</td>
      </tr>
      <tr>
        <td class="row_left">{#Groups_MaxPn#}</td>
        <td class="row_right"><input class="input" name="MaxPn" type="text" id="MaxPn" value="{$res->MaxPn}" size="4" maxlength="4" /></td>
      </tr>
      <tr>
        <td class="row_left">{#Groups_MaxC#}</td>
        <td class="row_right"><input class="input" name="MaxPn_Zeichen" type="text" value="{$res->MaxPn_Zeichen}" size="4" maxlength="5" /></td>
      </tr>
      <tr>
        <td colspan="2" class="headers_row">{#Groups_Sig#}</td>
      </tr>
      <tr>
        <td class="row_left">{#Groups_SigAllow#}</td>
        <td class="row_right">
          <label><input type="radio" name="Signatur_Erlaubt" value="1" {if $res->Signatur_Erlaubt == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="Signatur_Erlaubt" value="0" {if $res->Signatur_Erlaubt == 0}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td class="row_left">{#Groups_Syscode#}</td>
        <td class="row_right">
          <label><input type="radio" name="SysCode_Signatur" value="1" {if $res->SysCode_Signatur == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="SysCode_Signatur" value="0" {if $res->SysCode_Signatur == 0}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td class="row_left">{#Groups_MaxC#}</td>
        <td class="row_right"><input class="input" name="Signatur_Laenge" type="text" value="{$res->Signatur_Laenge}" size="4" maxlength="5" /></td>
      </tr>
    {/if}
    <tr>
      <td colspan="2" class="headers_row">{#Groups_PostF#}</td>
    </tr>
    <tr>
      <td class="row_left">{#Groups_MaxC#}</td>
      <td class="row_right"><input class="input" name="MaxZeichenPost" type="text" value="{$res->MaxZeichenPost}" size="4" maxlength="5" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Groups_MaxAttach#}</td>
      <td class="row_right"><input class="input" name="MaxAnlagen" type="text" value="{$res->MaxAnlagen}" size="4" maxlength="2" /></td>
    </tr>
    {if $smarty.request.id != 2}
      <tr>
        <td colspan="2" class="headers_row">{#Groups_Avatar#}</td>
      </tr>
      {if $avatar}
        <tr>
          <td class="row_left">{#Groups_AvatarCurrent#}</td>
          <td class="row_right">
            {$avatar}
            <br />
            <input name="Avdel" type="checkbox" id="Avdel" value="1" />{#Groups_DelAvatar#}
          </td>
        </tr>
      {/if}
      <tr>
        <td class="row_left">{#Groups_AvatarNew#}</td>
        <td class="row_right">
          {if $not_writable == 1}
            {#Groups_AvDirNw#}
          {else}
            <div id="UpInf_1"></div>
            <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
            <input id="fileToUpload_1" type="file" size="45" name="fileToUpload_1" class="input" />
            <input type="button" class="button" id="buttonUpload_1" onclick="return fileUpload('iconupload', 1);" value="{#UploadButton#}" />
            <input type="hidden" name="newImg_1" id="newFile_1" />
            {if perm('mediapool')}
              <input type="button" class="button" onclick="uploadBrowser('image', 'avatars', 1);" value="{#Global_ImgSel#}" />
            {/if}
          {/if}
        </td>
      </tr>
      <tr>
        <td class="row_left">{#Groups_MaxWidth#}</td>
        <td class="row_right"><input class="input" name="Avatar_B" id="Avatar_B" type="text" value="{$res->Avatar_B}" size="4" maxlength="3" /></td>
      </tr>
      <tr>
        <td class="row_left">{#Groups_MaxHeight#}</td>
        <td class="row_right"><input class="input" name="Avatar_H" type="text" value="{$res->Avatar_H}" size="4" maxlength="3" /></td>
      </tr>
      <tr>
        <td class="row_left">{#Groups_SetDefault#}</td>
        <td class="row_right">
          <label><input type="radio" name="Avatar_Default" value="1" {if $res->Avatar_Default == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="Avatar_Default" value="0" {if $res->Avatar_Default == 0}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
    {/if}
  </table>
  <input type="submit" value="{#Save#}" class="button" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
