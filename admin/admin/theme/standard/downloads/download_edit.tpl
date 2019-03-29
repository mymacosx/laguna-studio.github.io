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
function changeLang(langcode) {
    if(confirm('{#Global_changeLangDoc#}')) {
        location.href = 'index.php?do=downloads&sub=edit&id={$smarty.request.id}&noframes=1&langcode=' + langcode;
    } else{
        document.getElementById('l_{$smarty.request.langcode}').selected = true;
    }
}
//-->
</script>

<div class="header_inf">
  <form onsubmit="" method="post" action="">
    <select class="input" onchange="eval(this.options[this.selectedIndex].value);" name="langcode" id="langcode">
      <option id="l_1" value="changeLang(1);" {if isset($smarty.request.langcode) && $smarty.request.langcode == 1}selected="selected"{/if}>{#Download_editIn#} - {$language.name.1|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_2" value="changeLang(2);" {if isset($smarty.request.langcode) && $smarty.request.langcode == 2}selected="selected"{/if}>{#Download_editIn#} - {$language.name.2|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_3" value="changeLang(3);" {if isset($smarty.request.langcode) && $smarty.request.langcode == 3}selected="selected"{/if}>{#Download_editIn#} - {$language.name.3|upper} - {#Shop_articles_editinb#}</option>
    </select>
    <img class="absmiddle" src="{$imgpath}/arrow_right.png" alt="" />
  </form>
</div>
<form method="post" action="" enctype="multipart/form-data" name="sysform" id="sysform">
  {if isset($smarty.request.langcode) && $smarty.request.langcode<2}
    <div id="container-options">
      <ul>
        <li><a href="#opt-1"><span>{#Download_tabDescr#}</span></a></li>
              {if isset($smarty.request.langcode) && $smarty.request.langcode == 1}
          <li><a href="#opt-2"><span>{#FileUpload#}</span></a></li>
                {if $res->DefektGemeldet}
            <li><a href="#opt-4"><span>{#Links_broken#}</span></a></li>
                {/if}
          <li><a href="#opt-3"><span>{#Download_sysother#}</span></a></li>
              {/if}
      </ul>
    {/if}
    <div id="opt-1">
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td>
            <fieldset>
              <legend><label for="t">{#Global_Name#}</label></legend>
              <input name="Name" type="text" class="input" id="t" style="width: 180px" value="{$res->Name}" />
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend>{#Global_PublicDate#}</legend>
              <input name="Datum" id="Datum" type="text" class="input" style="width: 120px" value="{$res->Datum|date_format: '%d.%m.%Y'}" />
            </fieldset>
          </td>
          {if isset($smarty.request.langcode) && $smarty.request.langcode == 1}
            <td>
              <fieldset>
                <legend><label for="c">{#Global_Categ#}</label></legend>
                <select class="input" style="width: 220px" name="Kategorie">
                  {foreach from=$Categs item=c}{if $c->Parent_Id == 0}
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
                <label><input type="radio" name="Aktiv" value="1" {if $res->Aktiv == 1}checked="checked"{/if}/>{#Global_online#}</label>
                <label><input type="radio" name="Aktiv" value="0" {if $res->Aktiv == 0}checked="checked"{/if}/>{#Global_offline#}</label>
              </fieldset>
            </td>
            <td width="120">
              <fieldset>
                <legend>
                  <label for="spr">{#Links_ccode#}</label>
                  <span class="stip" title="{$lang.Links_ccodeInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>
                </legend>
                <input name="Sprache" type="text" class="input" id="spr" style="width: 50px" value="{$res->Sprache}" maxlength="2" />
              </fieldset>
            </td>
          {/if}
        </tr>
      </table>
      {if isset($smarty.request.langcode) && $smarty.request.langcode == 1}
        <fieldset>
          <legend>{#Image#}</legend>
          <div>
            {if !empty($res->Bild)}
              <img src="../uploads/downloads/{$res->Bild}" alt="" />
            {/if}
          </div>
          <label><input type="checkbox" name="NoImg" value="1" />{#Global_ImgDel#}</label>
        </fieldset>
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
      {/if}
      <fieldset>
        <legend>{#Content_text#}</legend>
        {$Beschreibung}
      </fieldset>
    </div>
    {if isset($smarty.request.langcode) && $smarty.request.langcode == 1}
      <div id="opt-2">
        <table width="100%" border="0" cellpadding="2" cellspacing="0">
          <tr>
            <td valign="top"><fieldset style="height: 160px">
                <legend>{#FileUpload#} </legend>
                {if !empty($res->Url)}
                  <strong>{#Download_upnCurrent#}: </strong><em>{$res->Url}</em>
                  <br />
                  <br />
                {/if}
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
            <td width="50%" valign="top"><fieldset style="height: 160px">
                <legend>{#Download_manu2#}</legend>
                {#Download_manu2Inf#}
                <br />
                <br />
                <table width="100%" border="0" cellspacing="1" cellpadding="1">
                  <tr>
                    <td>{#Download_url#}: </td>
                    <td><input name="Url_Direct" type="text" class="input" style="width: 200px" value="{$res->Url_Direct}" /></td>
                  </tr>
                  <tr>
                    <td>{#GlobalSize#}: </td>
                    <td><input name="Size_Direct" type="text" class="input" style="width: 50px" value="{$res->Size_Direct}" />{#Download_sizeInf#}</td>
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
      {if isset($smarty.request.langcode) && $smarty.request.langcode == 1}
        <div id="opt-3">
          <fieldset>
            <legend>{#Download_version#}</legend>
            <input name="Version" type="text" class="input" style="width: 200px" value="{$res->Version}" />
          </fieldset>
          <fieldset>
            <legend>{#Download_os#}</legend>
            <select name="BetriebsOs[]" size="8" multiple="multiple" class="input" style="width: 200px">
              {foreach from=$BetriebsOs item=o}
                <option value="{$o}" {if in_array($o,$BetriebsOsIn)}selected="selected"{/if}>{$o}</option>
              {/foreach}
            </select>
          </fieldset>
          <fieldset>
            <legend>{#Download_softtype#}</legend>
            <input name="SoftwareTyp" type="text" class="input" style="width: 200px" value="{$res->SoftwareTyp}" />
          </fieldset>
        </div>
        {if $res->DefektGemeldet}
          <div id="opt-4">
            <div class="subheaders">{#Links_brokenInf#}</div>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="150">{#Links_Link#}</td>
                <td><a href="{$res->Url}" target="_blank">{$res->Url}</a></td>
              </tr>
              <tr>
                <td>{#Links_broken_Reason#}</td>
                <td>{$res->DefektGemeldet}</td>
              </tr>
              <tr>
                <td>{#Links_broken_fromuser#}</td>
                <td>{$res->DName|sanitize} ({$res->DEmail})</td>
              </tr>
              <tr>
                <td>{#Links_broken_date#}</td>
                <td>{$res->DDatum|date_format: $lang.DateFormat}</td>
              </tr>
              <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
              </tr>
              <tr>
                <td>{#Links_broken_delm#}</td>
                <td><label><input type="checkbox" name="DelDM" value="1" />{#Yes#}</label></td>
              </tr>
            </table>
          </div>
        {/if}
      {/if}
    {/if}
    {if isset($smarty.request.langcode) && $smarty.request.langcode<2}
    </div>
  {/if}
  <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
  <input type="hidden" name="langcode" value="{$smarty.request.langcode|default:1}" />
  {if isset($smarty.request.langcode) && $smarty.request.langcode == 1}
    <label><input type="checkbox" name="saveAllLang" value="1" />{#SavDataAllLangs#}</label>
    {/if}
</form>
