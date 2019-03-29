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
	  Bilder_Klein: { required: true, range: [40,120] },
	  Bilder_Mittel: { required: true, range: [90,250] },
	  Bilder_Gross: { required: true, range: [320,1200] },
	  Transparenz: { required: true, range: [15,100] },
	  Bilder_Seite: { required: true, range: [20,100] },
	  Bilder_Zeile: { required: true, range: [2,5] },
	  Limit_Start: { required: true, range: [10,25] },
	  Quali_Gross: { required: true, range: [60,100] }
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

<div class="header">{#SettingsModule#} {#Gallery#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form name="editForm" id="editForm" method="post" action="">
  <div class="headers">
    {#Gallery_imgS#}
    <br />
    <small>{#Gallery_imgInf#}</small>
  </div>
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr>
      <td class="row_left">{#Gallery_settings_style#}</td>
      <td class="row_right">
        <label><input type="radio" name="GTyp" value="lightbox" {if $res.GTyp == 'lightbox'} checked="checked"{/if}/>{#Gallery_settings_stype2#}</label>
        <label><input type="radio" name="GTyp" value="standard" {if $res.GTyp == 'standard'} checked="checked"{/if}/>{#Gallery_settings_stype1#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left" width="250">{#Gallery_set_tsmall#}</td>
      <td class="row_right"><input name="Bilder_Klein" type="text" class="input" value="{$res.Bilder_Klein}" size="4" maxlength="3" /> {#Pixels#}</td>
    </tr>
    <tr>
      <td class="row_left">{#Gallery_set_tnormal#}</td>
      <td class="row_right"><input name="Bilder_Mittel" type="text" class="input" value="{$res.Bilder_Mittel}" size="4" maxlength="3" /> {#Pixels#}</td>
    </tr>
    <tr>
      <td class="row_left"><span class="stip" title="{$lang.Gallery_set_tbigInf}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Gallery_set_tbig#}</td>
      <td class="row_right"><input name="Bilder_Gross" type="text" class="input" value="{$res.Bilder_Gross}" size="4" maxlength="4" /> {#Pixels#}</td>
    </tr>
    <tr>
      <td class="row_left"><span class="stip" title="{$lang.Gallery_set_qualiInf}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Gallery_set_quali#}</td>
      <td class="row_right"><input name="Quali_Gross" type="text" class="input" value="{$res.Quali_Gross}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td class="row_left"><span class="stip" title="{$lang.Gallery_set_wmInf}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Gallery_set_wm#} ({#Gallery_settings_stylew#})</td>
      <td class="row_right">
        <label><input type="radio" name="Wasserzeichen_Vorschau" value="1" {if $res.Wasserzeichen_Vorschau == 1} checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="Wasserzeichen_Vorschau" value="0" {if $res.Wasserzeichen_Vorschau == 0} checked="checked"{/if}/>{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left"><span class="stip" title="{$lang.Gallery_set_wmdlInf}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Gallery_set_wmdl#} ({#Gallery_settings_stylew#})</td>
      <td class="row_right">
        <label><input type="radio" name="Wasserzeichen" value="1" {if $res.Wasserzeichen == 1} checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="Wasserzeichen" value="0" {if $res.Wasserzeichen == 0} checked="checked"{/if}/>{#No#}</label>
      </td>
    </tr>
    {if !empty($res.Watermark_File)}
      <tr>
        <td width="400" class="row_left">{#Shop_Settings_WatermarkCurrent#}</td>
        <td class="row_right">
          <img src="../uploads/watermarks/{$res.Watermark_File}?{$time}" alt="" border="" />
          <input type="hidden" name="watermark_old" value="{$res.Watermark_File}" />
        </td>
      </tr>
    {/if}
    <tr>
      <td width="400" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_Settings_WatermarkInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_Settings_WatermarkUp#}</td>
      <td class="row_right"><div id="UpInf_1"></div>
        <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="300" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_1" type="file" size="30" name="fileToUpload_1" class="input" />
        <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('watermark', 1);" value="{#UploadButton#}" />
        {if perm('mediapool')}
          <input type="button" class="button" onclick="uploadBrowser('image', 'watermarks', 1);" value="{#Global_ImgSel#}" />
        {/if}
        <input type="hidden" name="newImg_1" id="newFile_1" />
      </td>
    </tr>
    <tr>
      <td class="row_left">{#WatermarkPosistion#}</td>
      <td>
        <select style="width: 240px" class="input" name="Watermark_Position">
          <option value="bottom_right"{if $res.Watermark_Position == 'bottom_right'} selected="selected" {/if}>{#BottomRight#}</option>
          <option value="bottom_left"{if $res.Watermark_Position == 'bottom_left'} selected="selected" {/if}>{#BottomLeft#}</option>
          <option value="bottom_center"{if $res.Watermark_Position == 'bottom_center'} selected="selected" {/if}>{#BottomCenter#}</option>
          <option value="top_right"{if $res.Watermark_Position == 'top_right'} selected="selected" {/if}>{#TopRight#}</option>
          <option value="top_left"{if $res.Watermark_Position == 'top_left'} selected="selected" {/if}>{#TopLeft#}</option>
          <option value="top_center"{if $res.Watermark_Position == 'top_center'} selected="selected" {/if}>{#TopCenter#}</option>
          <option value="center_right"{if $res.Watermark_Position == 'center_right'} selected="selected" {/if}>{#CenterRight#}</option>
          <option value="center_left"{if $res.Watermark_Position == 'center_left'} selected="selected" {/if}>{#CenterLeft#}</option>
          <option value="center"{if $res.Watermark_Position == 'center'} selected="selected" {/if}>{#Center#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Settings_WmTrans#}</td>
      <td class="row_right"><input name="Transparenz" type="text" class="input" value="{$res.Transparenz}" size="4" maxlength="3" /> % </td>
    </tr>
    <tr>
      <td class="row_left">{#Gallery_renew#}</td>
      <td class="row_right">
        <label><input type="radio" name="renew" value="1" /> {#Yes#}</label>
        <label><input type="radio" name="renew" value="0" checked="checked"/> {#No#}</label>
      </td>
    </tr>
  </table>
  <div class="headers">{#Gallery_sLimit#}</div>
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr>
      <td width="250" class="row_left">{#GallerySortable#}</td>
      <td class="row_right">
        <select name="Sortierung_Start" class="input">
          <option value="ASC" {if $res.Sortierung_Start == 'ASC'}selected="selected" {/if}>{#asc_t#}</option>
          <option value="DESC" {if $res.Sortierung_Start == 'DESC'}selected="selected" {/if}>{#desc_t#}</option>
          <option value="RAND" {if $res.Sortierung_Start == 'RAND'}selected="selected" {/if}>{#RandomSortable#}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Gallery_set_ppsite#}</td>
      <td class="row_right"><input name="Bilder_Seite" type="text" class="input" value="{$res.Bilder_Seite}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Gallery_set_pprow#}</td>
      <td class="row_right"><input name="Bilder_Zeile" type="text" class="input" value="{$res.Bilder_Zeile}" size="4" maxlength="3" /></td>
    </tr>
    <tr>
      <td class="row_left">{#Gallery_set_categspp#}</td>
      <td class="row_right"><input name="Limit_Start" type="text" class="input" value="{$res.Limit_Start}" size="4" maxlength="3" /></td>
    </tr>
  </table>
  <div class="headers">{#Gallery_sOther#} (<small style="font-weight: normal">{#Gallery_settings_stylew#}</small>)</div>
  <table width="100%" border="0" cellspacing="0" cellpadding="1">
    <tr>
      <td class="row_left">{#GallerySettingsInfSmall#}</td>
      <td class="row_right">
        <label><input type="radio" name="Info_Klein" value="1" {if $res.Info_Klein == 1} checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="Info_Klein" value="0" {if $res.Info_Klein == 0} checked="checked"{/if}/>{#No#}</label>
      </td>
    </tr>
    <tr>
      <td width="250" class="row_left">{#Gallery_set_diareload#}</td>
      <td class="row_right">
        <label><input type="radio" name="Diashow_Zeit" value="3" {if $res.Diashow_Zeit == 3} checked="checked"{/if}/>3 сек.</label>
        <label><input type="radio" name="Diashow_Zeit" value="5" {if $res.Diashow_Zeit == 5} checked="checked"{/if}/>5 сек.</label>
        <label><input type="radio" name="Diashow_Zeit" value="10" {if $res.Diashow_Zeit == 10} checked="checked"{/if}/>10 сек.</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Gallery_set_showmosts#}</td>
      <td class="row_right">
        <label><input type="radio" name="Meist_Gesehen" value="1" {if $res.Meist_Gesehen == 1} checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="Meist_Gesehen" value="0" {if $res.Meist_Gesehen == 0} checked="checked"{/if}/>{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Gallery_set_dl#}</td>
      <td class="row_right">
        <label><input type="radio" name="Download" value="1" {if $res.Download == 1} checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="Download" value="0" {if $res.Download == 0} checked="checked"{/if}/>{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Set_comments#}</td>
      <td class="row_right">
        <label><input type="radio" name="Kommentare" value="1" {if $res.Kommentare == 1} checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="Kommentare" value="0" {if $res.Kommentare == 0} checked="checked"{/if}/>{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Gallery_set_favs#}</td>
      <td class="row_right">
        <label><input type="radio" name="Favoriten" value="1" {if $res.Favoriten == 1} checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="Favoriten" value="0" {if $res.Favoriten == 0} checked="checked"{/if}/>{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left"><span class="stip" title="{$lang.Gallery_set_bannercInf}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#BannersCode#}</td>
      <td class="row_right"><textarea name="Banner_Code" cols="40" rows="5" class="input" id="Banner_Code">{$res.Banner_Code|sanitize}</textarea></td>
    </tr>
  </table>
  <input type="submit" class="button" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
