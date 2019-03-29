<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
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
	url: 'index.php?do=partners&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
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

<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr>
      <td class="row_left" width="150">{#Image#}</td>
      <td class="row_right"> {if !empty($res->Bild)}<img src="../{$res->Bild}" alt="" />{/if} </td>
    </tr>
    <tr>
      <td class="row_left">{#Global_imgNew#}</td>
      <td class="row_right">
        <div id="UpInf_1"></div>
        <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_1" type="file" size="20" name="fileToUpload_1" class="input" />
        <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('iconupload', 1);" value="{#UploadButton#}" />
        {if perm('mediapool')}
          <input type="button" class="button" onclick="uploadBrowser('image', 'partner', 1);" value="{#Global_ImgSel#}" />
        {/if}
        <input type="hidden" name="newImg_1" id="newFile_1" />
      </td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" class="button" onclick="closeWindow(true);" value="{#Close#}" />
</form>
