{script file="$jspath/jupload.js" position='head'}
<script type="text/javascript">
<!-- //
function fileUpload(divid) {
    $(document).ajaxStart(function() {
        $('UpInf_' + divid).hide();
        $('#loading_' + divid).show();
        $('#buttonUpload_' + divid).val('{#Global_Wait#}').prop('disabled', true);
    }).ajaxComplete(function() {
        $('#loading_' + divid).hide();
        $('#buttonUpload_' + divid).val('{#UploadButton#}').prop('disabled', false);
    });
    $.ajaxFileUpload( {
	url: 'index.php?p=user&action=upload&divid=' + divid,
	secureuri: false,
	fileElementId: 'fileToUpload_' + divid,
	dataType: 'json',
	success: function (data) {
	    if(typeof(data.result) !== 'undefined') {
                document.getElementById('UpInf_' + divid).innerHTML = data.result;
                if(data.filename !== '') {
                    document.getElementById('newFile_' + divid).value = data.filename;
                    var nextid = eval(divid + '+' + 1);
                    $('#tab_' + nextid).show();
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


<div class="popup_header h4">{#NewAlbum#}</div>
<div id="body_blanc" style="padding: 5px" align="center">
  {if !empty($error)}
    <div class="error_box">
      <ul>
        {foreach from=$error item=err}
          <li>{$err}</li>
        {/foreach}
      </ul>
    </div>
    <input type="button" class="button" onclick="self.close();" value="{#WinClose#}" />
  {else}
    <form action="" method="post" enctype="multipart/form-data">
      <table width="100%" border="0" cellspacing="0" cellpadding="10">
        <tr>
          <td width="30%" align="left"><label for="l_gal_new_name"><strong>{#AlbumTitle#}</strong></label></td>
          <td align="left"><input name="title" type="text" class="input" id="l_gal_new_name" style="width: 98%" value="{$smarty.post.title|default:''|escape: html}" /></td>
        </tr>
      </table>
      <br />
      {section name='i' start=0 loop=$loop step=1}
        <table width="100%" border="0" cellspacing="0" cellpadding="0" id="tab_{$smarty.section.i.index}" style="padding: 10px;border: 1px solid #ddd; {if $smarty.section.i.index != 1 && $smarty.section.i.index != $next|default:'' && (empty($title[$smarty.section.i.index]) || $file[$smarty.section.i.index] == '')}display: none;{/if}">
          <tr valign="middle">
            <td width="20%">
              <fieldset style="background: #fafafa; border: 1px solid #eee;">
                <legend><strong><label for="l_new_{$smarty.section.i.index}">{#UploadAlbum#}</label></strong></legend>
                <input style="width: 190px;" class="input" type="text" name="feld_title[{$smarty.section.i.index}]" value="{$title[$smarty.section.i.index]|default:''}" />
                <p>
                  <input id="fileToUpload_{$smarty.section.i.index}" type="file" size="20" name="fileToUpload_{$smarty.section.i.index}" />
                  <input type="hidden" name="feld_file[{$smarty.section.i.index}]" id="newFile_{$smarty.section.i.index}" value="{$file[$smarty.section.i.index]|default:''}" />
                </p>
              </fieldset>
            </td>
            <td align="center">
              <div id="UpInf_{$smarty.section.i.index}">{$pic[$smarty.section.i.index]|default:''}</div>
              <div id="loading_{$smarty.section.i.index}" style="display: none;"><img src="{$imgpath_page}ajaxbar.gif" alt="" /></div>
              <p><input type="button" class="button" id="buttonUpload_{$smarty.section.i.index}" onclick="fileUpload('{$smarty.section.i.index}');" value="{#UploadButton#}" /></p>
            </td>
          </tr>
        </table>
      {/section}
      <br />
      <input type="hidden" name="save" value="1" />
      <input class="button" type="submit" value="{#Save#}" />
      <input type="button" class="button" onclick="window.opener.location.reload();self.close();" value="{#WinClose#}" />
    </form>
  {/if}
</div>

