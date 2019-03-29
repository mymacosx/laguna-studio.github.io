<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['editForm'].submit();
    }
});
$(document).ready(function() {
    $('#editForm').validate({
	rules: {
            Name_1: { required: true,minlength: 2 },
            Tags: { maxlength: 255 }
        },
	messages: { }
    });
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
        url: 'index.php?do=gallery&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
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

<div class="popbox">
  <form name="editForm" id="editForm" method="post" action="">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="180" class="row_left">{#Global_Name#} ({$language.name.1})</td>
        <td class="row_right"><input name="Name_1" type="text" class="input" value="{$res->Name_1|sanitize}" size="50" /></td>
      </tr>
      <tr>
        <td class="row_left">{#Global_Name#} ({$language.name.2})</td>
        <td class="row_right"><input name="Name_2" type="text" class="input" value="{$res->Name_2|sanitize}" size="50" /></td>
      </tr>
      <tr>
        <td class="row_left">{#Global_Name#} ({$language.name.3})</td>
        <td class="row_right"><input name="Name_3" type="text" class="input" value="{$res->Name_3|sanitize}" size="50" /></td>
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
      {if $res->Img}
        <tr>
          <td class="row_left">{#Gallery_CategIcon#}</td>
          <td class="row_right">
            {$res->Img}
            <br />
            <label><input type="checkbox" name="delold" value="1" />{#Global_ImgDel#} </label>
            <input type="hidden" name="oldimg" value="{$res->ImgPath}" />
          </td>
        </tr>
      {/if}
      <tr>
        <td class="row_left">{#Gallery_CategIconNew#}</td>
        <td class="row_right">
          <div id="UpInf_1"></div>
          <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
          <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="{$gs.Kategorie_Icon_Breite}" /> px. &nbsp;&nbsp;&nbsp;
          <input id="fileToUpload_1" type="file" size="45" name="fileToUpload_1" class="input" />
          <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('categicon', 1);" value="{#UploadButton#}" />
          {if perm('mediapool')}
            <input type="button" class="button" onclick="uploadBrowser('image', 'galerie_icons', 1);" value="{#Global_ImgSel#}" />
          {/if}
          <input type="hidden" name="newImg_1" id="newFile_1" />
        </td>
      </tr>
      <tr>
        <td class="row_left">{#Global_Active#}</td>
        <td class="row_right">
          <label><input type="radio" name="Aktiv" value="1" checked="checked" />{#Yes#}</label>
          <label><input type="radio" name="Aktiv" value="0" />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td class="row_left"><span class="stip" title="{$lang.GalleryTagHelp|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>{#Tags#} </td>
        <td class="row_right"><input type="text" name="Tags" class="input" value="{$res->Tags|sanitize}" style="width: 90%" maxlength="255" /></td>
      </tr>
    </table>
    <input type="submit" class="button" value="{#Save#}" />
    <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
    <input name="save" type="hidden" id="save" value="1" />
  </form>
</div>
