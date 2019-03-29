<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#new').validate({
	rules: {
	    PartnerName: { required: true },
	    PartnerUrl: { maxlength: 255, url: true, required: true }
	},
	messages: { },
	submitHandler: function(){
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

<div class="header">{#Partners#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="" name="kform">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td width="100" class="headers">{#Image#}</td>
      <td width="100" class="headers">{#Partners_url#}</td>
      <td width="100" class="headers">{#Global_Name#}</td>
      <td width="50" align="center" class="headers">{#Global_Hits#}</td>
      <td width="50" align="center" class="headers">{#Global_Position#}</td>
      <td width="90" align="center" class="headers">Nofollow</td>
      <td width="90" align="center" class="headers">{#Global_Active#}</td>
      <td width="50" class="headers">{#Global_Actions#}</td>
      <td class="headers"><label></label></td>
    </tr>
    {foreach from=$partners item=g}
      <tr class="{cycle values='second,first'}">
        <td> {if !empty($g->Bild)} <img style="width: 150px;" src="../{$g->Bild}" alt="" /> {/if} </td>
        <td><input class="input" type="text" name="PartnerUrl[{$g->Id}]" value="{$g->PartnerUrl}" /></td>
        <td><input class="input" style="width: 100px" type="text" name="PartnerName[{$g->Id}]" value="{$g->PartnerName}" /></td>
        <td align="center"><input class="input" style="width: 30px" type="text" name="Hits[{$g->Id}]" value="{$g->Hits}" /></td>
        <td align="center"><input class="input" style="width: 30px" type="text" name="Position[{$g->Id}]" value="{$g->Position}" /></td>
        <td align="center" nowrap="nowrap">
          <label><input type="radio" name="Nofollow[{$g->Id}]" value="1" {if $g->Nofollow == 1} checked="checked"{/if}/>{#Yes#}</label>
          <label><input type="radio" name="Nofollow[{$g->Id}]" value="0" {if $g->Nofollow == 0} checked="checked"{/if}/>{#No#}</label>
        </td>
        <td align="center" nowrap="nowrap">
          <label><input type="radio" name="Aktiv[{$g->Id}]" value="1" {if $g->Aktiv == 1} checked="checked"{/if}/>{#Yes#}</label>
          <label><input type="radio" name="Aktiv[{$g->Id}]" value="0" {if $g->Aktiv == 0} checked="checked"{/if}/>{#No#}</label>
        </td>
        <td>
          <a class="colorbox_small stip" title="{$lang.Edit|sanitize}" href="index.php?do=partners&amp;sub=edit&amp;id={$g->Id}&amp;noframes=1"><img src="{$imgpath}/edit.png" alt="" border="0" /></a>
          <a class="stip" title="{$lang.Global_Delete|sanitize}" onclick="return confirm('{#ConfirmGlobal#}{$g->PartnerName|jsspecialchars}');" href="index.php?do=partners&amp;sub=del&amp;id={$g->Id}&amp;backurl={$backurl}"><img src="{$imgpath}/delete.png" alt="" border="0" /></a>
        </td>
        <td><label></label></td>
      </tr>
    {/foreach}
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
<br />
{if !empty($Navi)}
  <div class="navi_div"> {$Navi} </div>
{/if}
<br />
<fieldset>
  <legend>{#Partners_new#}</legend>
  <form method="post" action="" name="new" id="new">
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100">{#Image#}</td>
        <td>
          <div id="UpInf_1"></div>
          <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
          <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="150" /> px.&nbsp;&nbsp;&nbsp;
          <input id="fileToUpload_1" type="file" size="40" name="fileToUpload_1" class="input" />
          <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('iconupload', 1);" value="{#UploadButton#}" />
          {if perm('mediapool')}
            <input type="button" class="button" onclick="uploadBrowser('image', 'partner', 1);" value="{#Global_ImgSel#}" />
          {/if}
          <input type="hidden" name="newImg_1" id="newFile_1" />
        </td>
      </tr>
      <tr>
        <td>{#Global_Name#}</td>
        <td><input class="input" style="width: 300px" type="text" name="PartnerName" value="" /></td>
      </tr>
      <tr>
        <td>{#Partners_url#}</td>
        <td><input class="input" style="width: 300px" type="text" name="PartnerUrl" value="" /></td>
      </tr>
      <tr>
        <td>Nofollow</td>
        <td>
          <label><input type="radio" name="Nofollow" value="1" checked="checked" />{#Yes#}</label>
          <label><input type="radio" name="Nofollow" value="0" />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td>{#Global_Position#}</td>
        <td><input class="input" style="width: 30px" type="text" name="Position" value="1" /></td>
      </tr>
    </table>
    <input type="hidden" name="new" value="1" />
    <input type="submit" class="button" value="{#Save#}" />
  </form>
</fieldset>
