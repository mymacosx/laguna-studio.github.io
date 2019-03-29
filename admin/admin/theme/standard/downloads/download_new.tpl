<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['sysform'].submit();
    }
});
$(document).ready(function() {
    $('#q').on('click', function() {
        $(this).suggest('index.php?do=downloads&sub=filesuggest&key=' + Math.random());
    });

    $('#sysform').validate({
        ignore: '#container-options',
        rules: {
	    Name: { required: true },
	    Url_Direct: { },
	    Size_Direct: { number: true }
	},
        messages: {
	    Url: { url: '{#InserURL#}' }
	}
    });
    $('#Datum').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
    $('#container-options').tabs({
        selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	    $('#current_tabs').val(ui.index);
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
    if(divid == 1) {
        var resize = document.getElementById('resizeUpload_' + divid).value;
        var up = 'index.php?do=downloads&sub=' + sub + '&divid=' + divid + '&resize=' + resize;
    } else {
        var up = 'index.php?do=downloads&sub=' + sub + '&divid=' + divid;
    }
    $.ajaxFileUpload({
	url: up,
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

<form method="post" action="" enctype="multipart/form-data" name="sysform" id="sysform">
  <div id="container-options">
    <ul>
      <li><a href="#opt-1"><span>{#Download_tabDescr#}</span></a></li>
      <li><a href="#opt-2"><span>{#FileUpload#}</span></a></li>
      <li><a href="#opt-3"><span>{#Download_sysother#}</span></a></li>
    </ul>
    <div id="opt-1">
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td>
            <fieldset>
              <legend><label for="t">{#Global_Name#}</label></legend>
              <input name="Name" type="text" class="input" id="t" style="width: 180px" value="{$res->Name|date_format: $lang.DateFormat}" />
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend>{#Global_PublicDate#}</legend>
              <input name="Datum" id="Datum" type="text" class="input" style="width: 120px" value="{$res->Datum|date_format: '%d.%m.%Y'}" />
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend><label for="c">{#Global_Categ#}</label></legend>
              <select class="input" style="width: 220px" name="Kategorie">
                {foreach from=$Categs item=c}
                  {if $c->Parent_Id == 0}
                    <option style="font-weight: bold" value="{$c->Id}" {if $c->Id == $res->Kategorie}selected="selected"{/if}>{$c->Name}</option>
                  {else}
                    <option value="{$c->Id}" {if $c->Id == $res->Kategorie}selected="selected"{/if}>{$c->visible_title}</option>
                  {/if}
                {/foreach}
              </select>
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend>{#Global_Status#}</legend>
              <label><input type="radio" name="Aktiv" value="1" checked="checked" />{#Global_online#}</label>
              <label><input type="radio" name="Aktiv" value="0" />{#Global_offline#}</label>
            </fieldset>
          </td>
          <td width="120">
            <fieldset>
              <legend>
                <label for="spr">{#Links_ccode#}</label>
                <span class="stip" title="{$lang.Links_ccodeInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>
              </legend>
              <input name="Sprache" type="text" class="input" id="spr" style="width: 50px" value="ru" maxlength="2" />
            </fieldset>
          </td>
        </tr>
      </table>
      <fieldset>
        <legend>{#Global_imgNew#}</legend>
        {if $wrietable_img == 1}
          <div id="UpInf_1"></div>
          <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
          <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
          <input id="fileToUpload_1" type="file" size="30" name="fileToUpload_1" class="input" />
          <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('iconupload', 1);" value="{#UploadButton#}" />
          {if perm('mediapool')}
            <input type="button" class="button" onclick="uploadBrowser('image', 'downloads', 1);" value="{#Global_ImgSel#}" />
          {/if}
          <input type="hidden" name="newImg_1" id="newFile_1" />
        {else}
          <strong style="color: #FF0000">{#Download_nwIMG#}</strong>
        {/if}
      </fieldset>
      <fieldset><legend>{#Content_text#}</legend>{$Beschreibung}</fieldset>
    </div>
    <div id="opt-2">
      <table width="100%" border="0" cellpadding="2" cellspacing="0">
        <tr>
          <td valign="top"><fieldset style="height: 160px">
              <legend>{#FileUpload#} </legend>
              <strong>{#Download_upnew#} (max. {$post_maxMb})</strong>
              <span class="stip" title="{$lang.Download_upnewInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>
                {if $wrietable == 1}
                <div id="UpInf_2"></div>
                <div id="loading_2" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
                <input id="fileToUpload_2" type="file" size="38" name="fileToUpload_2" class="input" />
                <input type="button" class="button" id="buttonUpload_2" onclick="fileUpload('fileupload', 2);" value="{#UploadButton#}" />
                {if perm('mediapool')}
                  <input type="button" class="button" onclick="uploadBrowser('file', 'downloads_files', 2);" value="{#Global_ImgSel#}" />
                {/if}
                <input type="hidden" name="newImg_2" id="newFile_2" />
              {else}
                <br />
                <strong style="color: #FF0000">{#Download_nwDL#}</strong>
              {/if}
              <br />
              <br />
              <strong>{#Download_manu#}</strong>
              <span class="stip" title="{$lang.Download_manuInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>
              <br />
              <input class="input" name="q" type="text" id="q" style="width: 300px" value="" />
            </fieldset>
          </td>
          <td width="50%" valign="top">
            <fieldset style="height: 160px">
              <legend>{#Download_manu2#}</legend>
              {#Download_manu2Inf#}
              <br />
              <br />
              <table width="100%" border="0" cellspacing="1" cellpadding="1">
                <tr>
                  <td>{#Download_url#}: </td>
                  <td><input name="Url_Direct" type="text" class="input" id="url" style="width: 200px" value="{$res->Url_Direct}" /></td>
                </tr>
                <tr>
                  <td>{#GlobalSize#}: </td>
                  <td><input name="Size_Direct" type="text" class="input" id="url" style="width: 50px" value="{$res->Size_Direct}" />{#Download_sizeInf#}</td>
                </tr>
              </table>
            </fieldset>
          </td>
        </tr>
      </table>
      <fieldset>
        <legend>{#Download_mirrors#}</legend>
        {#Download_mirrorsInf#}
        <br />
        <br />
        <textarea cols="" rows=""  name="Mirrors" style="width: 99%; height: 77px">{$res->Mirrors}</textarea>
      </fieldset>
    </div>
    <div id="opt-3">
      <fieldset>
        <legend>{#Download_version#}</legend>
        <input name="Version" type="text" class="input" style="width: 200px" value="{$res->Version}" />
      </fieldset>
      <fieldset>
        <legend>{#Download_os#}</legend>
        <select name="BetriebsOs[]" size="8" multiple="multiple" class="input" style="width: 200px">
          {foreach from=$BetriebsOs item=o}
            <option value="{$o}">{$o}</option>
          {/foreach}
        </select>
      </fieldset>
      <fieldset>
        <legend>{#Download_softtype#}</legend>
        <input name="SoftwareTyp" type="text" class="input" style="width: 200px" value="" />
      </fieldset>
    </div>
  </div>
  <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
  <input type="hidden" name="langcode" value="{$smarty.request.langcode|default:1}" />
</form>
