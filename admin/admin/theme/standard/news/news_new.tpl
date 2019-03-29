<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults( {
    submitHandler: function() {
	document.forms['newsForm'].submit();
        showNotice('<h2>{#Global_Wait#}</h2>', 2000);
    }
});

$(document).ready(function() {
    $('#container-options').tabs({
        selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	    $('#current_tabs').val(ui.index);
	}
    });

    $('#ZeitStart').datepicker({ changeMonth: true, changeYear: true, dateFormat: 'dd.mm.yy', dayNamesMin: [{#Calendar_daysmin#}], monthNamesShort: [{#Calendar_monthNamesShort#}], firstDay: 1 });
    $('#ZeitEnde').datepicker({ changeMonth: true, changeYear: true, dateFormat: 'dd.mm.yy', dayNamesMin: [{#Calendar_daysmin#}], monthNamesShort: [{#Calendar_monthNamesShort#}], firstDay: 1 });

    $('#newsForm').validate({
        ignore: '#container-options',
	rules: {
	    {if isset($smarty.request.langcode) && $smarty.request.langcode == 1}
	    ZeitStart: { required: true },
	    {/if}
	    Titel: { required: true }
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
    $.ajaxFileUpload( {
	url: 'index.php?do=news&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
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

<div class="subheaders">{#News_multiInf#}</div>
<form method="post" action="" enctype="multipart/form-data" name="newsForm" id="newsForm">
  <div id="container-options">
    <ul>
      <li><a href="#opt-1"><span>{#News_tab_gen#}</span></a></li>
      <li><a href="#opt-3"><span>{#Image#}</span></a></li>
      <li><a href="#opt-4"><span>{#News_isTopnews#}</span></a></li>
      <li><a href="#opt-5"><span>{#Global_Inline#}</span></a></li>
      <li><a href="#opt-6"><span>{#News_tab_other#}</span></a></li>
    </ul>
    <div id="opt-1">
      <table width="100%">
        <tr>
          <td>
            <fieldset>
              <legend><label for="t">{#Global_Name#}</label></legend>
              <input name="Titel" type="text" class="input" id="t" style="width: 200px" value="{$news->Titel}" />
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend><label for="c">{#Global_Categ#}</label></legend>
              <select  style="width: 160px" class="input" id="c" name="categ">
                {foreach from=$newscategs item=dd}
                  <option value="{$dd->Id}" {if $news->Kategorie == $dd->Id}selected="selected"{/if}>{$dd->visible_title} </option>
                {/foreach}
              </select>
            </fieldset>
          </td>
          <td align="center">
            <fieldset>
              <legend><label for="ZeitStart">{#Global_PublicDate#}</label></legend>
              <input name="ZeitStart" type="text" class="input" id="ZeitStart" style="width: 100px" value="{$smarty.now|date_format: '%d.%m.%Y'}" readonly="readonly" />
            </fieldset>
          </td>
          <td align="center">
            <fieldset>
              <legend><label for="ZeitEnde">{#Global_PublicEndDate#}</label></legend>
              <input name="ZeitEnde" type="text" class="input" id="ZeitEnde" style="width: 100px" value="{if $news->ZeitEnde != '0'}{$news->ZeitEnde|date_format: '%d.%m.%Y'}{/if}" />
            </fieldset>
          </td>
        </tr>
      </table>
      <table width="100%">
        <tr>
          <td valign="top">
            <fieldset>
              <legend>{#Global_Status#}</legend>
              <label><input type="radio" name="Aktiv" value="1" checked="checked"/>{#Global_online#}</label>
              <label><input type="radio" name="Aktiv" value="0" />{#Global_offline#}</label>
            </fieldset>
          </td>
          <td valign="top">
            <fieldset>
              <legend>{#News_show_allsections#}</legend>
              <label><input type="radio" name="AlleSektionen" value="1" />{#Yes#}</label>
              <label><input type="radio" name="AlleSektionen" value="0" checked="checked"/>{#No#}</label>
            </fieldset>
          </td>
          <td valign="top">
            <fieldset>
              <legend>{#News_searchable#}</legend>
              <label><input type="radio" name="Suche" value="1" checked="checked"/>{#Yes#}</label>
              <label><input type="radio" name="Suche" value="0" />{#No#}</label>
            </fieldset>
          </td>
          <td valign="top">
            <fieldset>
              <legend>{#News_voteable#}</legend>
              <label><input type="radio" name="Bewertung" value="1" checked="checked" />{#Yes#}</label>
              <label><input type="radio" name="Bewertung" value="0" />{#No#}</label>
            </fieldset>
          </td>
          <td valign="top">
            <fieldset>
              <legend>{#News_commentable#}</legend>
              <label><input type="radio" name="Kommentare" value="1" checked="checked" />{#Yes#}</label>
              <label><input type="radio" name="Kommentare" value="0" />{#No#}</label>
            </fieldset>
          </td>
        </tr>
      </table>
      <fieldset>
        <legend><span class="stip" title="{$lang.News_tab_teaserInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>&nbsp;{#News_tab_teaser#}</legend>
            {$Intro}
      </fieldset>
      <fieldset>
        <legend>{#News_text#}</legend>
        {$News}
        {include file="$incpath/other/fckinserts.tpl"}
        <img class="absmiddle stip" title="{$lang.Global_newInf|sanitize}" src="{$imgpath}/help.png" alt="" />
        <a href="javascript: void(0);" onclick="insertEditor('News','[--NEU--]')">{#Global_newPage#}</a>
      </fieldset>
    </div>
    <div id="opt-3">
      <fieldset>
        <legend>{#Image#}</legend>
        <div>{if !empty($news->Bild)}<img src="{$news->Bild}" alt="" />{/if}</div>
        <label><input type="checkbox" name="NoImg" value="1" />{#Global_ImgDel#}</label>
      </fieldset>
      <fieldset>
        <legend>{#Global_imgNew#}</legend>
        <div id="UpInf_1"></div>
        <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_1" type="file" size="20" name="fileToUpload_1" class="input" />
        <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('iconupload', 1);" value="{#UploadButton#}" />
        {if perm('mediapool')}
          <input type="button" class="button" onclick="uploadBrowser('image', 'news', 1);" value="{#Global_ImgSel#}" />
        {/if}
        <input type="hidden" name="Bild_1" id="newFile_1" />
      </fieldset>
      <fieldset>
        <legend>{#Global_imgAlign#}</legend>
        <label><input type="radio" name="BildAusrichtung" value="right" {if $news->BildAusrichtung == 'right' || $smarty.request.BildAusrichtung == 'right' || empty($news->BildAusrichtung)}checked="checked"{/if} /> {#Global_ImgRight#}</label>
        <label><input type="radio" name="BildAusrichtung" value="left" {if $news->BildAusrichtung == 'left'  || $smarty.request.BildAusrichtung == 'left'}checked="checked"{/if} /> {#Global_ImgLeft#}</label>
      </fieldset>
    </div>
    <div id="opt-4">
      <fieldset>
        <legend>{#News_isTopnews#}</legend>
        <label><input type="radio" name="Topnews" value="1" />{#Yes#}</label>
        <label><input type="radio" name="Topnews" value="0" checked="checked" />{#No#}</label>
        <br />
        <br />
        {#News_TopnewsInf#}
      </fieldset>
      <fieldset>
        <legend>{#Global_imgNew#}</legend>
        <div id="UpInf_2"></div>
        <div id="loading_2" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_2" type="text" size="3" name="resizeUpload_2" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_2" type="file" size="20" name="fileToUpload_2" class="input" />
        <input type="button" class="button" id="buttonUpload_2" onclick="fileUpload('iconupload', 2);" value="{#UploadButton#}" />
        {if perm('mediapool')}
          <input type="button" class="button" onclick="uploadBrowser('image', 'news', 2);" value="{#Global_ImgSel#}" />
        {/if}
        <input type="hidden" name="Bild_2" id="newFile_2" />
      </fieldset>
    </div>
    <div id="opt-5">
      {assign var='inline_table' value='news'}
      {assign var='fieldname' value=$field_inline}
      {include file="$incpath/screenshots/load.tpl"}
    </div>
    <div id="opt-6">
      <fieldset>
        <legend><span class="stip" title="{$lang.Tag_info|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Tags#} </legend>
        <input type="text" class="input" style="width: 99%" name="Tags" value="{$news->Tags}" />
      </fieldset>
      <fieldset>
        <legend><span class="stip" title="{$lang.Gal_incl_info|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Global_mergeGalleries#}</legend>
            {strip}
          <select name="Gallery[]" size="15" multiple  class="input" id="select" style="width: 250px">
            <option value="" selected="selected"> ---------- {#Sys_off#} ---------- </option>
            {foreach from=$Gallery item=ng}
              <optgroup label="{$ng->CategName}"></optgroup>
              {foreach from=$ng->Gals item=g}
                <option value="{$g->GalId}">{$g->GalName}</option>
              {/foreach}
            {/foreach}
          </select>
        {/strip}
      </fieldset>
    </div>
  </div>
  <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
  <input type="hidden" name="langcode" value="{$smarty.request.langcode|default:1}" />
</form>
