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
  <div class="header">{#Shop_downloads_title#}</div>
  <div class="subheaders"><small>{#Shop_downloads_titleInf#}</small></div>
  <form method="post" action="">
    <input type="hidden" name="id" value="{$smarty.request.id}" />
    <input type="hidden" name="subaction" value="save" />
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td class="headers" align="center"><img src="{$imgpath}/delete.png" alt="" /></td>
        <td width="150" class="headers">{#Shop_downloads_hdl#}</td>
        <td width="110" class="headers">{#Shop_downloads_hdlt#}</td>
        <td class="headers">{#Shop_downloads_hdln#}</td>
        <td class="headers">{#Global_descr#}</td>
        <td class="headers">{#Global_Position#}</td>
      </tr>
      <tr>
        <td colspan="6" class="subheaders"><strong>{#Shop_downloads_full#}</strong></td>
      </tr>
      {foreach from=$downloads_full item=dl}
        <tr class="{cycle values='first,second'}">
          <td width="1%" align="center"><input class="stip" title="{$lang.Global_Delete|sanitize}" type="checkbox" name="Del[{$dl->Id}]" value="1" /></td>
          <td width="130">
            <select class="input" style="width: 210px" name="Datei[{$dl->Id}]">
              {foreach from=$esds item=dlf}
                <option value="{$dlf}" {if $dl->Datei == $dlf}selected="selected"{/if}>{$dlf}</option>
              {/foreach}
            </select>
          </td>
          <td width="110">
            <select class="input" style="width: 110px" name="DateiTyp[{$dl->Id}]">
              <option value="full" {if $dl->DateiTyp == 'full'}selected="selected"{/if}>{#Shop_downloads_full#}</option>
              <option value="update" {if $dl->DateiTyp == 'update'}selected="selected"{/if}>{#Shop_downloads_update#}</option>
              <option value="bugfix" {if $dl->DateiTyp == 'bugfix'}selected="selected"{/if}>{#Shop_downloads_bugfix#}</option>
              <option value="other" {if $dl->DateiTyp == 'other'}selected="selected"{/if}>{#Shop_downloads_other#}</option>
            </select>
          </td>
          <td><input class="input" style="width: 210px" name="Titel[{$dl->Id}]" type="text" value="{$dl->Titel|sanitize}" /></td>
          <td><textarea cols="" rows="" class="input" wrap="xoff" title="{#Shop_downloads_otherDlTimeClick#}" style="width: 280px;height: 50px" onclick="focusArea(this, 200);" name="Beschreibung[{$dl->Id}]">{$dl->Beschreibung|sanitize}</textarea></td>
          <td><input class="input" name="Position[{$dl->Id}]" type="text" size="2" maxlength="3" value="{$dl->Position}" /></td>
        </tr>
      {/foreach}
      <tr>
        <td colspan="6" class="subheaders"><strong>{#Shop_downloads_update#}</strong></td>
      </tr>
      {foreach from=$downloads_updates item=dl}
        <tr class="{cycle values='first,second'}">
          <td width="1%" align="center"><input class="stip" title="{$lang.Global_Delete|sanitize}" type="checkbox" name="Del[{$dl->Id}]" value="1" /></td>
          <td width="130">
            <select class="input" style="width: 210px" name="Datei[{$dl->Id}]">
              {foreach from=$esds item=dlf}
                <option value="{$dlf}" {if $dl->Datei == $dlf}selected="selected"{/if}>{$dlf}</option>
              {/foreach}
            </select>
          </td>
          <td width="110">
            <select class="input" style="width: 110px" name="DateiTyp[{$dl->Id}]">
              <option value="full" {if $dl->DateiTyp == 'full'}selected="selected"{/if}>{#Shop_downloads_full#}</option>
              <option value="update" {if $dl->DateiTyp == 'update'}selected="selected"{/if}>{#Shop_downloads_update#}</option>
              <option value="bugfix" {if $dl->DateiTyp == 'bugfix'}selected="selected"{/if}>{#Shop_downloads_bugfix#}</option>
              <option value="other" {if $dl->DateiTyp == 'other'}selected="selected"{/if}>{#Shop_downloads_other#}</option>
            </select>
          </td>
          <td><input class="input" style="width: 210px" name="Titel[{$dl->Id}]" type="text" value="{$dl->Titel|sanitize}" /></td>
          <td><textarea cols="" rows="" class="input" wrap="xoff" title="{#Shop_downloads_otherDlTimeClick#}" style="width: 280px;height: 50px" onclick="focusArea(this, 200);" name="Beschreibung[{$dl->Id}]">{$dl->Beschreibung|sanitize}</textarea></td>
          <td><input class="input" name="Position[{$dl->Id}]" type="text" size="2" maxlength="3" value="{$dl->Position}" /></td>
        </tr>
      {/foreach}
      <tr>
        <td colspan="6" class="subheaders"><strong>{#Shop_downloads_bugfix#}</strong></td>
      </tr>
      {foreach from=$downloads_bugfixes item=dl}
        <tr class="{cycle values='first,second'}">
          <td width="1%" align="center"><input class="stip" title="{$lang.Global_Delete|sanitize}" type="checkbox" name="Del[{$dl->Id}]" value="1" /></td>
          <td width="130">
            <select class="input" style="width: 210px" name="Datei[{$dl->Id}]">
              {foreach from=$esds item=dlf}
                <option value="{$dlf}" {if $dl->Datei == $dlf}selected="selected"{/if}>{$dlf}</option>
              {/foreach}
            </select>
          </td>
          <td width="110">
            <select class="input" style="width: 110px" name="DateiTyp[{$dl->Id}]">
              <option value="full" {if $dl->DateiTyp == 'full'}selected="selected"{/if}>{#Shop_downloads_full#}</option>
              <option value="update" {if $dl->DateiTyp == 'update'}selected="selected"{/if}>{#Shop_downloads_update#}</option>
              <option value="bugfix" {if $dl->DateiTyp == 'bugfix'}selected="selected"{/if}>{#Shop_downloads_bugfix#}</option>
              <option value="other" {if $dl->DateiTyp == 'other'}selected="selected"{/if}>{#Shop_downloads_other#}</option>
            </select>
          </td>
          <td><input class="input" style="width: 210px" name="Titel[{$dl->Id}]" type="text" value="{$dl->Titel|sanitize}" /></td>
          <td><textarea cols="" rows="" class="input" wrap="xoff" title="{#Shop_downloads_otherDlTimeClick#}" style="width: 280px;height: 50px" onclick="focusArea(this, 200);" name="Beschreibung[{$dl->Id}]">{$dl->Beschreibung|sanitize}</textarea></td>
          <td><input class="input" name="Position[{$dl->Id}]" type="text" size="2" maxlength="3" value="{$dl->Position}" /></td>
        </tr>
      {/foreach}
      <tr>
        <td colspan="6" class="subheaders"><strong>{#Shop_downloads_other#}</strong></td>
      </tr>
      {foreach from=$downloads_other item=dl}
        <tr class="{cycle values='first,second'}">
          <td width="1%" align="center"><input class="stip" title="{$lang.Global_Delete|sanitize}" type="checkbox" name="Del[{$dl->Id}]" value="1" /></td>
          <td width="130">
            <select class="input" style="width: 210px" name="Datei[{$dl->Id}]">
              {foreach from=$esds item=dlf}
                <option value="{$dlf}" {if $dl->Datei == $dlf}selected="selected"{/if}>{$dlf}</option>
              {/foreach}
            </select>
          </td>
          <td width="110">
            <select class="input" style="width: 110px" name="DateiTyp[{$dl->Id}]">
              <option value="full" {if $dl->DateiTyp == 'full'}selected="selected"{/if}>{#Shop_downloads_full#}</option>
              <option value="update" {if $dl->DateiTyp == 'update'}selected="selected"{/if}>{#Shop_downloads_update#}</option>
              <option value="bugfix" {if $dl->DateiTyp == 'bugfix'}selected="selected"{/if}>{#Shop_downloads_bugfix#}</option>
              <option value="other" {if $dl->DateiTyp == 'other'}selected="selected"{/if}>{#Shop_downloads_other#}</option>
            </select>
          </td>
          <td><input class="input" style="width: 210px" name="Titel[{$dl->Id}]" type="text" value="{$dl->Titel|sanitize}" /></td>
          <td><textarea cols="" rows="" class="input" wrap="xoff" title="{#Shop_downloads_otherDlTimeClick#}" style="width: 280px;height: 50px" onclick="focusArea(this, 200);" name="Beschreibung[{$dl->Id}]">{$dl->Beschreibung|sanitize}</textarea></td>
          <td><input name="Position[{$dl->Id}]" type="text" size="2" maxlength="3" value="{$dl->Position}" /></td>
        </tr>
      {/foreach}
    </table>
    <br />
    <input type="submit" class="button" value="{#Save#}" />
  </form>
  <br />
  <br />
  <div class="header">{#Shop_downloads_newfile#}</div>
  <form method="post" action="">
    <input type="hidden" name="id" value="{$smarty.request.id}" />
    <input type="hidden" name="subaction" value="new" />
    <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
      <tr>
        <td width="150" class="headers">{#Shop_downloads_hdl#}</td>
        <td width="110" class="headers">{#Shop_downloads_hdlt#}</td>
        <td class="headers">{#Shop_downloads_hdln#}</td>
        <td class="headers">{#Global_descr#}</td>
        <td class="headers">{#Global_Position#}</td>
      </tr>
      <tr class="second">
        <td width="320" valign="top" nowrap="nowrap">
          <select class="input" style="width: 325px" name="Datei">
            {foreach from=$esds item=dlf}
              <option value="{$dlf}">{$dlf}</option>
            {/foreach}
          </select>
          <br />
          <strong>{#UploadNew#}</strong>
          <br />
          {if $can_upload == 1}
            <div id="UpInf_1"></div>
            <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
            <input id="fileToUpload_1" type="file" size="25" name="fileToUpload_1" class="input" />
            <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('esd_upload', 1);" value="{#UploadButton#}" />
            {if perm('mediapool')}
              <input type="button" class="button" onclick="uploadBrowser('file', 'shop/files', 1);" value="{#Global_ImgSel#}" />
            {/if}
            <input type="hidden" name="newFile_1" id="newFile_1" />
          {else}
            <small>{#UploadWritableError#}</small>
          {/if}
        </td>
        <td width="110" valign="top">
          <select class="input" style="width: 110px" name="DateiTyp">
            <option value="full">{#Shop_downloads_full#}</option>
            <option value="update">{#Shop_downloads_update#}</option>
            <option value="bugfix">{#Shop_downloads_bugfix#}</option>
            <option value="other">{#Shop_downloads_other#}</option>
          </select>
        </td>
        <td valign="top"><input class="input" style="width: 130px" name="Titel" type="text" value="Название" /></td>
        <td valign="top"><textarea cols="" rows="" class="input" wrap="xoff" title="{#Shop_downloads_otherDlTimeClick#}" style="width: 230px;height: 50px" onclick="focusArea(this, 200);" name="Beschreibung"></textarea></td>
        <td valign="top"><input class="input" name="Position" type="text" size="2" maxlength="3" value="1" /></td>
      </tr>
    </table>
    <input type="submit" class="button" value="{#Save#}" />
    <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
  </form>
</div>
