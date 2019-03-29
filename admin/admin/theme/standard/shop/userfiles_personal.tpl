<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#new').validate({
	rules: {
            newFile_1: { required: true },
            Beschreibung: { required: true }
        },
	messages: {
            Beschreibung: { required: ''}
        },
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
    $.ajaxFileUpload({
	url: 'index.php?do=shop&sub=' + sub + '&divid=' + divid,
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
  {if $userfiles}
    <form method="post" action="">
      <table width="100%" border="0" cellpadding="5" cellspacing="0" class="tableborder">
        <tr>
          <td class="headers">{#FileUpload#}</td>
          <td class="headers">{#Global_descr#}</td>
          <td class="headers">{#Global_Date#}</td>
          <td class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
        </tr>
        {foreach from=$userfiles item=uf}
          <tr class="{cycle values='second,first'}">
            <td valign="top" class="row_spacer"><strong><a href="index.php?do=shop&amp;sub=user_downloads_personal&amp;getfile={$uf->Id}">{$uf->Datei}</a></strong>
              <input type="hidden" name="Datei[{$uf->Id}]" style="width: 300px" value="{$uf->Datei}" /></td>
            <td valign="top" class="row_spacer"><textarea cols="" rows="" class="input" style="width: 500px; height: 50px" name="Beschreibung[{$uf->Id}]">{$uf->Beschreibung|sanitize}</textarea></td>
            <td valign="top" class="row_spacer">{$uf->Datum|date_format: '%d.%m.%Y'}</td>
            <td valign="top" class="row_spacer"><input class="stip" title="{$lang.Global_Delete|sanitize}" name="Del[{$uf->Id}]" type="checkbox" value="1" /></td>
          </tr>
        {/foreach}
      </table>
      <input type="submit" class="button" value="{#Save#}" />
      <input name="save" type="hidden" id="save" value="1" />
    </form>
    <br />
  {/if}
  <fieldset>
    <legend>{#Download_add#}</legend>
    <form method="post" action="" id="new" name="new">
      <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
        <tr>
          <td width="20%" class="row_left">{#FileUpload#}</td>
          <td class="row_right">
            <div id="UpInf_1"></div>
            <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
            <input id="fileToUpload_1" type="file" size="25" name="fileToUpload_1" class="input" />
            <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('user_personal_file', 1);" value="{#UploadButton#}" />
            {if perm('mediapool')}
              <input type="button" class="button" onclick="uploadBrowser('file', 'shop/customerfiles', 1);" value="{#Global_ImgSel#}" />
            {/if}
            <input type="hidden" name="newFile_1" id="newFile_1" />
          </td>
        </tr>
        <tr>
          <td width="20%" class="row_left">{#Global_descr#}</td>
          <td class="row_right"><textarea cols="" rows="" style="width: 500px; height: 50px" name="Beschreibung"></textarea></td>
        </tr>
      </table>
      <br />
      <input type="submit" class="button" value="{#Save#}" />
      <input name="new" type="hidden" value="1" />
    </form>
  </fieldset>
</div>
