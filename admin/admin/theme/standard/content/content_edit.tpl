{assign var='langcode' value=$smarty.request.langcode|default:1}
<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['newsForm'].submit();
    }
});

$(document).ready(function() {
     $('#container-options').tabs({
         selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	    $('#current_tabs').val(ui.index);
	}
    });
    $('#newsForm').validate({
        ignore: '#container-options',
        rules: {
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
	url: 'index.php?do=content&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
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
    if(confirm('{#Global_changeLangDoc#}')){
        location.href='index.php?do=content&sub=editcontent&id={$smarty.request.id}&noframes=1&langcode=' + langcode;
    } else {
        document.getElementById('l_{$langcode}').selected=true;
    }
}
//-->
</script>

<div class="header_inf">
  <form onsubmit="" method="post" action="">
    <select class="input" onchange="eval(this.options[this.selectedIndex].value);" name="langcode" id="langcode">
      <option id="l_1" value="changeLang(1);" {if $langcode == 1}selected="selected"{/if}>{#Shop_articles_editin#} - {$language.name.1|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_2" value="changeLang(2);" {if $langcode == 2}selected="selected"{/if}>{#Shop_articles_editin#} - {$language.name.2|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_3" value="changeLang(3);" {if $langcode == 3}selected="selected"{/if}>{#Shop_articles_editin#} - {$language.name.3|upper} - {#Shop_articles_editinb#}</option>
    </select>
    <img class="absmiddle" src="{$imgpath}/arrow_right.png" alt="" />
  </form>
</div>
<div id="container-options">
  <ul>
    <li><a href="#opt-1"><span>{#News_tab_gen#}</span></a></li>
    <li><a href="#opt-3"><span>{#Image#}</span></a></li>
    <li><a href="#opt-4"><span>{#Content_Topcontent#}</span></a></li>
          {if $langcode == 1}
      <li><a href="#opt-4a"><span>{#Forums_editPGroups#}</span></a></li>
          {/if}
    <li><a href="#opt-5"><span>{#Global_Inline#}</span></a></li>
          {if $langcode == 1}
      <li><a href="#opt-6"><span>{#News_tab_other#}</span></a></li>
          {/if}
  </ul>
  <div id="opt-1">
    <div class="doc_link_inf"> {#Content_Link#} <strong>index.php?p=content&amp;id={$content->Id}&amp;name={$content->Titel|translit}&amp;area={$content->Sektion}</strong></div>
    <form method="post" action="" enctype="multipart/form-data" name="newsForm" id="newsForm">
      <table width="100%">
        <tr>
          <td>
            <fieldset>
              <legend><label for="t">{#Global_Name#} ({$language.name.$langcode})</label></legend>
              <input name="Titel" type="text" class="input" id="t" style="width: 300px" value="{$content->Titel}" />
            </fieldset>
          </td>
          <td>
            {if $langcode == 1}
              <fieldset>
                <legend><label for="c">{#Global_Categ#}</label></legend>
                <select  style="width: 160px" class="input" id="c" name="categ">
                  {foreach from=$newscategs item=dd}
                    <option value="{$dd->Id}" {if $content->Kategorie == $dd->Id}selected="selected"{/if}>{$dd->Name|sanitize} </option>
                  {/foreach}
                </select>
              </fieldset>
            </td>
            <td>
              <fieldset>
                <legend>
                  <label for="k">{#LoginPass#}</label>
                  <span class="stip" title="{$lang.Global_DocPass|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>
                </legend>
                <input name="Kennwort" type="text" class="input" id="k" style="width: 200px" value="{$content->Kennwort}" />
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
                <label><input type="radio" name="Aktiv" value="1" {if $content->Aktiv == 1}checked="checked"{/if}/>{#Global_online#}</label>
                <label><input type="radio" name="Aktiv" value="0" {if $content->Aktiv == 0}checked="checked"{/if}/>{#Global_offline#}</label>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>{#News_searchable#}</legend>
                <label><input type="radio" name="Suche" value="1" {if $content->Suche == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="Suche" value="0" {if $content->Suche == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>{#News_voteable#}</legend>
                <label><input type="radio" name="Bewertung" value="1" {if $content->Bewertung == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="Bewertung" value="0" {if $content->Bewertung == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>{#News_commentable#}</legend>
                <label><input type="radio" name="Kommentare" value="1" {if $content->Kommentare == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="Kommentare" value="0" {if $content->Kommentare == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
          </tr>
        </table>
      {/if}
      <fieldset>
        <legend>{#Content_text#}  ({$language.name.$langcode})</legend>
        {$Content}
        {include file="$incpath/other/fckinserts.tpl"}
        <span class="stip" title="{$lang.Global_newInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>
        <a href="javascript: void(0);" onclick="insertEditor('Content','[--NEU--]');">{#Global_newPage#}</a>
      </fieldset>
  </div>
  <div id="opt-3">
    <fieldset>
      <legend>{#Image#}</legend>
      <div>
        {if !empty($content->Bild)}
          <img src="../uploads/content/{$content->Bild}" alt="" />
        {/if}
      </div>
      <label><input type="checkbox" name="NoImg" value="1" />{#Global_ImgDel#}</label>
    </fieldset>
    <fieldset>
      <legend>{#Global_imgNew#}</legend>
      <div id="UpInf_1"></div>
      <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
      <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
      <input id="fileToUpload_1" type="file" size="30" name="fileToUpload_1" class="input" />
      <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('iconupload', 1);" value="{#UploadButton#}" />
      {if perm('mediapool')}
        <input type="button" class="button" onclick="uploadBrowser('image', 'content', 1);" value="{#Global_ImgSel#}" />
      {/if}
      <input type="hidden" name="Bild_1" id="newFile_1" />
    </fieldset>
    <fieldset>
      <legend>{#Global_imgAlign#}</legend>
      <label><input type="radio" name="BildAusrichtung" value="right" {if $content->BildAusrichtung == 'right' || $smarty.request.BildAusrichtung == 'right' || empty($content->BildAusrichtung)}checked="checked"{/if} /> {#Global_ImgRight#}</label>
      <label><input type="radio" name="BildAusrichtung" value="left" {if $content->BildAusrichtung == 'left'  || $smarty.request.BildAusrichtung == 'left'}checked="checked"{/if} /> {#Global_ImgLeft#}</label>
    </fieldset>
  </div>
  <div id="opt-4">
    {if $langcode == 1}
      <fieldset>
        <legend>{#Content_isTop#}</legend>
        <label><input type="radio" name="Topcontent" value="1" {if $content->Topcontent == 1}checked="checked"{/if}/>{#Yes#}</label>
        <label><input type="radio" name="Topcontent" value="0" {if $content->Topcontent == 0}checked="checked"{/if}/>{#No#}</label>
        <br />
        <br />
        {#Content_TopcontentInf#}
      </fieldset>
    {else}
      <input name="Topcontent" type="hidden" value="{$content->Topcontent}" />
    {/if}
    <fieldset>
      <legend>{#Content_currTopImg#}</legend>
      <div>
        {if !empty($content->Topcontent_Bild)}
          <img src="../uploads/content/{$content->Topcontent_Bild}" alt="" />
        {/if}
      </div>
      <label><input type="checkbox" name="NoContentImg" value="1" />{#Global_ImgDel#}</label>
    </fieldset>
    <fieldset>
      <legend>{#Global_imgNew#}</legend>
      <div id="UpInf_2"></div>
      <div id="loading_2" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
      <input id="resizeUpload_2" type="text" size="3" name="resizeUpload_2" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
      <input id="fileToUpload_2" type="file" size="30" name="fileToUpload_2" class="input" />
      <input type="button" class="button" id="buttonUpload_2" onclick="fileUpload('iconupload', 2);" value="{#UploadButton#}" />
      {if perm('mediapool')}
        <input type="button" class="button" onclick="uploadBrowser('image', 'content', 2);" value="{#Global_ImgSel#}" />
      {/if}
      <input type="hidden" name="Bild_2" id="newFile_2" />
    </fieldset>
  </div>
  {if $langcode == 1}
    <div id="opt-4a">
      <fieldset>
        <legend>{#Forums_editPGroups#}</legend>
        <em> {#Docs_AllowedGroupsInf#} </em>
        <br />
        <br />
        <select name="Gruppen[]" size="10" multiple="multiple" class="input" style="width: 250px">
          {foreach from=$UserGroups item=group}
            <option value="{$group->Id}" {if in_array($group->Id,$content->Gruppe_Perms)}selected="selected" {/if}{if $group->Id == 1}disabled="disabled" {/if}>{$group->Name_Intern}</option>
          {/foreach}
        </select>
      </fieldset>
    </div>
  {/if}
  <div id="opt-5">
    {assign var='inline_table' value='content'}
    {assign var='fieldname' value=$field_inline}
    {include file="$incpath/screenshots/load.tpl"}
  </div>
  {if $langcode == 1}
    <div id="opt-6">
      <fieldset>
        <legend>{#Content_toN#} <span class="stip" title="{$lang.Content_toNinf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></legend>
        <label><input type="checkbox" name="ToNavi" value="1" {if $content->inNavi}checked="checked" disabled="disabled"{/if}/>{#Yes#}</label>
          {if !$content->inNavi}
          / {#Global_Position#}
          <input class="input" style="width: 20px" type="text" name="PosN" value="1" />
          <select style="display: none" name="Groups[]" size="1" multiple="multiple" class="input">
            {foreach from=$UserGroups item=group}
              <option value="{$group->Id}" selected="selected">{$group->Name_Intern}</option>
            {/foreach}
          </select>
          / {#Content_toNNav#}
          <select name="NaviCat2" class="input" style="width: 155px">
            {foreach from=$Navis item=n}
              <option style="font-weight: bold" value="{$n->Id}|0">{$n->Name_1}</option>
              {foreach from=$n->Items item=i}
                <option value="{$n->Id}|{$i->Id}">- {$i->Titel_1}</option>
              {/foreach}
            {/foreach}
          </select>
        {else}
          <em>{#Content_isInQN#}</em>
        {/if}
      </fieldset>
      <fieldset>
        <legend>{#Content_toQN#} <span class="stip" title="{$lang.Content_toQNinf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></legend>
        <label>
          <input type="checkbox" name="ToQuickNavi" value="1" {if $content->inQuicknavi}checked="checked" disabled="disabled"{/if} />{#Yes#}</label>
          {if !$content->inQuicknavi}
          / {#Global_Position#}
          <input class="input" style="width: 20px" type="text" name="PosQN" value="1" />
        {/if}
        {if $content->inQuicknavi}
          <em>{#Content_isInQN#}</em>
        {/if}
      </fieldset>
      <fieldset>
        <legend><span class="stip" title="{$lang.Tag_info|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Tags#} </legend>
        <input type="text" class="input" style="width: 99%" name="Tags" value="{$content->Tags}" />
      </fieldset>
      <fieldset>
        <legend><span class="stip" title="{$lang.Gal_incl_info|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>{#Global_mergeGalleries#}</legend>
            {strip}
          <select name="Gallery[]" size="15" multiple  class="input" id="select" style="width: 250px">
            <option value="" {if empty($content->Galerien.0)}selected="selected"{/if}> ---------- {#Sys_off#} ---------- </option>
            {foreach from=$Gallery item=ng}
              <optgroup label="{$ng->CategName}"></optgroup>
              {foreach from=$ng->Gals item=g}
                <option value="{$g->GalId}" {if in_array($g->GalId, $content->Galerien)}selected="selected"{/if}>{$g->GalName}</option>
              {/foreach}
            {/foreach}
          </select>
        {/strip}
      </fieldset>
    </div>
    <label><input type="checkbox" name="saveAllLang" value="1" />{#SavDataAllLangs#}</label>
    <br />
  {/if}
  <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
  <input type="hidden" name="langcode" value="{$langcode}" />
</form>
</div>