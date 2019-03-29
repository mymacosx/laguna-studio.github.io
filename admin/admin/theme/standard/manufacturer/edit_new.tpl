<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['Form'].submit();
    }
});
$(document).ready(function() {
    $('#Form').validate({
        rules: {
	    Name: { required: true },
	    Homepage: { required: true,url: true }
	},
        messages: { }
    });
    $('#Datum').datepicker({ changeMonth: true, changeYear: true, dateFormat: 'dd.mm.yy', dayNamesMin: [{#Calendar_daysmin#}], monthNamesShort: [{#Calendar_monthNamesShort#}], firstDay: 1 });
});
function fileUpload(sub, divid) {
    $(document).ajaxStart(function() {
        $('#loading_' + divid).show();
        $('#buttonUpload_' + divid).val('{#Global_Wait#}').prop('disabled', true);
    }).ajaxComplete(function() {
        $('#loading_' + divid).hide();
        $('#buttonUpload_' + divid).val('{#UploadButton#}').prop('disabled', false);
    });
    var resize = document.getElementById('resizeUpload_' + divid).value;
    $.ajaxFileUpload({
	url: 'index.php?do=manufacturer&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
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

<form name="Form" id="Form" action="" method="post">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="200" class="row_left">{#Global_Name#}</td>
      <td class="row_right"><input class="input" style="width: 200px" type="text" name="Name" value="{$res->Name|sanitize}" /></td>
    </tr>

    <tr>
      <td width="200" class="row_left">{#Global_PublicDate#}</td>
      <td class="row_right"><input class="input" style="width: 200px" type="text" name="Datum" id="Datum" value="{$res->Datum|date_format: '%d.%m.%Y'}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Manufacturer_fullname#}</td>
      <td class="row_right"><input class="input" style="width: 200px" type="text" name="NameLang" value="{$res->NameLang|sanitize}" /></td>
    </tr>
    {if !empty($res->Bild)}
      <tr>
        <td class="row_left">{#Image#}</td>
        <td class="row_right">
          <img src="../uploads/manufacturer/{$res->Bild}" alt="" />
          <br />
          <label><input type="checkbox" name="NoImg" value="1" />{#Global_ImgDel#}</label>
        </td>
      </tr>
    {/if}
    <tr>
      <td class="row_left">{#Global_imgNew#}</td>
      <td class="row_right">
        <div id="UpInf_1"></div>
        <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_1" type="file" size="20" name="fileToUpload_1" class="input" />
        <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('iconupload', 1);" value="{#UploadButton#}" />
        <input type="hidden" name="newImg_1" id="newFile_1" />
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Global_descr#} ({$language.name.1})</td>
      <td class="row_right">{$Beschreibung_1}</td>
    </tr>
    <tr>
      <td class="row_left">{#Global_descr#} ({$language.name.2})</td>
      <td class="row_right">{$Beschreibung_2}</td>
    </tr>
    <tr>
      <td class="row_left">{#Global_descr#} ({$language.name.3})</td>
      <td class="row_right">{$Beschreibung_3}</td>
    </tr>
    <tr>
      <td class="row_left">{#Manufacturer_home#}</td>
      <td class="row_right"><input class="input" style="width: 200px" type="text" name="Homepage" value="{$res->Homepage|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#User_country#}</td>
      <td class="row_right"><input class="input" style="width: 200px" type="text" name="GruendungLand" value="{$res->GruendungLand|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Manufacturer_gr_year#}</td>
      <td class="row_right"><input class="input" style="width: 50px" type="text" name="Gruendung" value="{$res->Gruendung|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Manufacturer_worker#}</td>
      <td class="row_right"><input class="input" style="width: 50px" type="text" name="Personen" value="{$res->Personen|sanitize}"/></td>
    </tr>
    <tr>
      <td class="row_left">{#Manufacturer_phone#}</td>
      <td class="row_right"><input class="input" style="width: 200px" type="text" name="Telefonkontakt" value="{$res->Telefonkontakt|sanitize}" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Manufacturer_adress#}</td>
      <td class="row_right"><textarea cols="" rows="" class="input" style="width: 300px; height: 100px" name="Adresse" id="Adresse">{$res->Adresse|sanitize}</textarea></td>
    </tr>
  </table>
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
