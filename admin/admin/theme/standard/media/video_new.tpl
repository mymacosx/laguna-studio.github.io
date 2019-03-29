<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#new').validate({
	rules: {
            Name: { required : true },
            Breite: { required: true },
            Hoehe: { required: true }
        },
	messages: { },
	submitHandler: function() {
	    document.forms['new'].submit();
	}
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
    $.ajaxFileUpload ( {
	url: 'index.php?do=media&sub=' + sub + '&divid=' + divid,
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

<div class="popheaders">{#MediaHint#}</div>
<form method="post" action="" id="new" name="new">
  <fieldset>
    <legend>{#Mediaopt1#}/uploads/videos/</legend>
    <select class="input" style="width: 500px" name="Datei">
      <option value=""></option>
      {foreach from=$folderVideo item=dlf}
        <option value="{$dlf->File}" {if !empty($dlf->FileInDb)}disabled="disabled"{/if}>{$dlf->File} {if !empty($dlf->FileInDb)} - (Название в базе: {$dlf->FileInDb}){/if}</option>
      {/foreach}
    </select>
  </fieldset>
  <fieldset>
    <legend>{#Mediaopt2#}(xxx.flv) <img class="absmiddle stip" title="{$lang.MediaFTPInf|sanitize}/uploads/videos/" src="{$imgpath}/help.png" alt="" /></legend>
      {if $can_upload == 1}
      <div id="UpInf_1"></div>
      <div id="loading_1" style="display: none"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
      <input id="fileToUpload_1" type="file" size="45" name="fileToUpload_1" class="input" />
      <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('videoupload', 1);" value="{#UploadButton#}" />
      {if perm('mediapool')}
        <input type="button" class="button" onclick="uploadBrowser('video', 'videos', 1);" value="{#Global_ImgSel#}" />
      {/if}
      <input type="hidden" name="newFile_1" id="newFile_1" />
    {else}
      <small>{#MediaNW#}&bdquo;/uploads/videos/&ldquo;</small>
    {/if}
  </fieldset>
  <fieldset>
    <legend>{#Global_props#}</legend>
    <table>
      <tr>
        <td>{#Global_Name#}: </td>
        <td><input class="input" style="width: 350px" type="text" name="Name" value="" /></td>
      </tr>
      <tr>
        <td>{#GlobalWidth#}: </td>
        <td><input class="input" type="text" name="Breite" value="500px" /> (например: 500px или 100%)</td>
      </tr>
      <tr>
        <td>{#GlobalHeight#}: </td>
        <td><input class="input" type="text" name="Hoehe" value="400px" /> (например: 400px)</td>
      </tr>
    </table>
  </fieldset>
  <br />
  <input class="button" type="submit" value="{#Save#}" />
  <input class="button" type="button" onclick="closeWindow();" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
