{assign var='langcode' value=$smarty.request.langcode|default:1}
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

    $('#ZeitStart, #ZeitEnde').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });

    $('#newsForm').validate({
        ignore: '#container-options',
	rules: {
	    {if $langcode == 1}
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
function changeLang(langcode) {
    if(confirm('{#Global_changeLangDoc#}')) {
        location.href='index.php?do=news&sub=editnews&id={$smarty.request.id}&noframes=1&langcode=' + langcode;
    } else {
        document.getElementById('l_{$langcode}').selected=true;
    }
}
//-->
</script>

<div class="header_inf">
  <form onsubmit="" method="post" action="">
    <select class="input" onchange="eval(this.options[this.selectedIndex].value)" name="langcode" id="langcode">
      <option id="l_1" value="changeLang(1);" {if $langcode == 1}selected="selected"{/if}>{#Shop_articles_editin#} - {$language.name.1|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_2" value="changeLang(2);" {if $langcode == 2}selected="selected"{/if}>{#Shop_articles_editin#} - {$language.name.2|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_3" value="changeLang(3);" {if $langcode == 3}selected="selected"{/if}>{#Shop_articles_editin#} - {$language.name.3|upper} - {#Shop_articles_editinb#}</option>
    </select>
    <img class="absmiddle" src="{$imgpath}/arrow_right.png" alt="" />
  </form>
</div>
<form method="post" action="" enctype="multipart/form-data" name="newsForm" id="newsForm">
  <div id="container-options">
    <ul>
      <li><a href="#opt-1"><span>{#News_tab_gen#}</span></a></li>
      <li><a href="#opt-3"><span>{#Image#}</span></a></li>
      <li><a href="#opt-4"><span>{#News_isTopnews#}</span></a></li>
      <li><a href="#opt-5"><span>{#Global_Inline#}</span></a></li>
            {if $langcode == 1}
        <li><a href="#opt-6"><span>{#News_tab_other#}</span></a></li>
            {/if}
    </ul>
    <div id="opt-1">
      <table width="100%">
        <tr>
          <td>
            <fieldset>
              <legend><label for="t">{#Global_Name#} ({$language.name.$langcode})</label></legend>
              <input name="Titel" type="text" class="input" id="t" style="width: 300px" value="{$news->Titel}" />
            </fieldset>
          </td>
          <td>
            {if $langcode == 1}
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
                <input name="ZeitStart" type="text" class="input" id="ZeitStart" style="width: 100px" value="{$news->ZeitStart|date_format: '%d.%m.%Y'}" readonly="readonly" />
              </fieldset>
            </td>
            <td align="center">
              <fieldset>
                <legend><label for="ZeitEnde">{#Global_PublicEndDate#}</label></legend>
                <input name="ZeitEnde" type="text" class="input" id="ZeitEnde" style="width: 100px" value="{if $news->ZeitEnde != '0'}{$news->ZeitEnde|date_format: '%d.%m.%Y'}{/if}" readonly="readonly" />
              </fieldset>
            </td>
          {/if}
        </tr>
      </table>
      {if $langcode == 1}
        <table width="100%">
          <tr>
            <td valign="top">
              <fieldset>
                <legend>{#Global_Status#}</legend>
                <label><input type="radio" name="Aktiv" value="1" {if $news->Aktiv == 1}checked="checked"{/if}/>{#Global_online#}</label>
                <label><input type="radio" name="Aktiv" value="0" {if $news->Aktiv == 0}checked="checked"{/if}/>{#Global_offline#}</label>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>{#News_show_allsections#}</legend>
                <label><input type="radio" name="AlleSektionen" value="1" {if $news->AlleSektionen == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="AlleSektionen" value="0" {if $news->AlleSektionen == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>{#News_searchable#}</legend>
                <label><input type="radio" name="Suche" value="1" {if $news->Suche == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="Suche" value="0" {if $news->Suche == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>{#News_voteable#}</legend>
                <label><input type="radio" name="Bewertung" value="1" {if $news->Bewertung == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="Bewertung" value="0" {if $news->Bewertung == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>{#News_commentable#}</legend>
                <label><input type="radio" name="Kommentare" value="1" {if $news->Kommentare == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="Kommentare" value="0" {if $news->Kommentare == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
          </tr>
        </table>
      {/if}
      <fieldset>
        <legend><span class="stip" title="{$lang.News_tab_teaserInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>&nbsp;{#News_tab_teaser#} ({$language.name.$langcode})</legend>
            {$Intro}
      </fieldset>
      <fieldset>
        <legend>{#News_text#} ({$language.name.$langcode})</legend>
        {$News}
        {include file="$incpath/other/fckinserts.tpl"}
        <img class="absmiddle stip" title="{$lang.Global_newInf|sanitize}" src="{$imgpath}/help.png" alt="" />
        <a href="javascript: void(0);" onclick="insertEditor('News','[--NEU--]')">{#Global_newPage#}</a>
      </fieldset>
    </div>
    <div id="opt-3">
      <fieldset>
        <legend>{#Image#}</legend>
        <div>
          {if !empty($news->Bild)}
            <img src="../uploads/news/{$news->Bild}" alt="" />
          {/if}
        </div>
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
      {if $langcode == 1}
        <fieldset>
          <legend>{#News_isTopnews#}</legend>
          <label><input type="radio" name="Topnews" value="1" {if $news->Topnews == 1}checked="checked"{/if}/>{#Yes#}</label>
          <label><input type="radio" name="Topnews" value="0" {if $news->Topnews == 0}checked="checked"{/if}/>{#No#}</label>
          <br />
          <br />
          {#News_TopnewsInf#}
        </fieldset>
      {else}
        <input name="Topcontent" type="hidden" value="{$news->Topnews}" />
      {/if}
      <fieldset>
        <legend>{#Global_TopnewsimgCurrent#}</legend>
        <div>
          {if !empty($news->Topnews_Bild)}
            <img src="../uploads/news/{$news->Topnews_Bild}" alt="" />
          {/if}
        </div>
        <label><input type="checkbox" name="NoTopnewsImg" value="1" />{#Global_ImgDel#}</label>
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
    {if $langcode == 1}
      <div id="opt-6">
        <fieldset>
          <legend><span class="stip" title="{$lang.Tag_info|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Tags#} </legend>
          <input type="text" class="input" style="width: 99%" name="Tags" value="{$news->Tags}" />
        </fieldset>
        <fieldset>
          <legend><span class="stip" title="{$lang.Gal_incl_info|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Global_mergeGalleries#}</legend>
              {strip}
            <select name="Gallery[]" size="15" multiple  class="input" id="select" style="width: 250px">
              <option value="" {if empty($news->Galerien.0)}selected="selected"{/if}> ---------- {#Sys_off#} ---------- </option>
              {foreach from=$Gallery item=ng}
                <optgroup label="{$ng->CategName}"></optgroup>
                {foreach from=$ng->Gals item=g}
                  <option value="{$g->GalId}" {if in_array($g->GalId, $news->Galerien)}selected="selected"{/if}>{$g->GalName}</option>
                {/foreach}
              {/foreach}
            </select>
          {/strip}
        </fieldset>
      </div>
    {/if}
    {if $langcode == 1}
      <label><input type="checkbox" name="saveAllLang" value="1" />{#SavDataAllLangs#}</label>
      <br />
    {/if}
  </div>
  <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
  <input type="hidden" name="langcode" value="{$langcode}" />
</form>
