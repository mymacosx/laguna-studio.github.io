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
    $('#ZeitStart, #ZeitEnde').datepicker({ changeMonth: true,changeYear: true,dateFormat: 'dd.mm.yy',dayNamesMin: [{#Calendar_daysmin#}],monthNamesShort: [{#Calendar_monthNamesShort#}],firstDay: 1 });

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
    var resize = document.getElementById('resizeUpload_' + divid).value;
    $.ajaxFileUpload({
	url: 'index.php?do=articles&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
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
        location.href='index.php?do=articles&sub=edit&id={$smarty.request.id}&noframes=1&langcode=' + langcode;
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
            {if $langcode == 1}
        <li><a href="#opt-2"><span>{#GlobalDetails#}</span></a></li>
            {/if}
      <li><a href="#opt-3"><span>{#Image#}</span></a></li>
      <li><a href="#opt-4"><span>{#GlobalTops#}</span></a></li>
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
              <input name="Titel" type="text" class="input" id="t" style="width: 300px" value="{$article->Titel}" />
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend><label for="tu">{#Gaming_articles_subt#}</label></legend>
              <input name="Untertitel" type="text" class="input" id="tu" style="width: 300px" value="{$article->Untertitel}" />
            </fieldset>
          </td>
          {if $langcode == 1}
            <td>
              <fieldset>
                <legend><label for="c">{#Global_Categ#}</label></legend>
                <select  style="width: 120px" class="input" id="c" name="categ">
                  {foreach from=$articlecategs item=dd}
                    <option value="{$dd->Id}" {if $article->Kategorie == $dd->Id}selected="selected"{/if}>{$dd->visible_title} </option>
                  {/foreach}
                </select>
              </fieldset>
            {/if}
          </td>
        </tr>
      </table>
      {if $langcode == 1}
        <table width="100%">
          <tr>
            <td>
              <fieldset>
                <legend>
                  <label for="at">{#Global_Type#}</label>
                  &nbsp; <span class="stip" title="{$lang.Gaming_articles_typInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>
                </legend>
                <select style="width: 150px" id="at" class="input" name="Typ">
                  <option value="special" {if $article->Typ == 'special'}selected="selected"{/if}>{#Gaming_ArtType_special#} </option>
                  <option value="review" {if $article->Typ == 'review'}selected="selected"{/if}>{#Global_Overview#} </option>
                  <option value="preview" {if $article->Typ == 'preview'}selected="selected"{/if}>{#Gaming_ArtType_preview#} </option>
                </select>
              </fieldset>
            </td>
            <td align="center">
              <fieldset>
                <legend><label for="ZeitStart">{#Global_PublicDate#}</label></legend>
                <input name="ZeitStart" type="text" class="input" id="ZeitStart" style="width: 70px" value="{$article->ZeitStart|date_format: '%d.%m.%Y'}" readonly="readonly" />
              </fieldset>
            </td>
            <td align="center">
              <fieldset>
                <legend><label for="ZeitEnde">{#Global_PublicEndDate#}</label></legend>
                <input name="ZeitEnde" type="text" class="input" id="ZeitEnde" style="width: 70px" value="{if $article->ZeitEnde != '0'}{$article->ZeitEnde|date_format: '%d.%m.%Y'}{/if}" readonly="readonly" />
              </fieldset>
            </td>
            <td>
              <fieldset>
                <legend>{#Global_Status#}</legend>
                <label><input type="radio" name="Aktiv" value="1" {if $article->Aktiv == 1}checked="checked"{/if}/>{#Global_online#}</label>
                <label><input type="radio" name="Aktiv" value="0" {if $article->Aktiv == 0}checked="checked"{/if}/>{#Global_offline#}</label>
              </fieldset>
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td valign="top">
              <fieldset>
                <legend>{#News_show_allsections#}</legend>
                <label><input type="radio" name="AlleSektionen" value="1" {if $article->AlleSektionen == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="AlleSektionen" value="0" {if $article->AlleSektionen == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>{#News_searchable#}</legend>
                <label><input type="radio" name="Suche" value="1" {if $article->Suche == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="Suche" value="0" {if $article->Suche == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>{#News_voteable#}</legend>
                <label><input type="radio" name="Wertung" value="1" {if $article->Wertung == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="Wertung" value="0" {if $article->Wertung == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>{#News_commentable#}</legend>
                <label><input type="radio" name="Kommentare" value="1" {if $article->Kommentare == 1}checked="checked"{/if}/>{#Yes#}</label>
                <label><input type="radio" name="Kommentare" value="0" {if $article->Kommentare == 0}checked="checked"{/if}/>{#No#}</label>
              </fieldset>
            </td>
          </tr>
        </table>
      {/if}
      <fieldset>
        <legend>{#News_text#} ({$language.name.$langcode})</legend>
        {$Inhalt}
        {include file="$incpath/other/fckinserts.tpl"}
        <img class="absmiddle stip" title="{$lang.Global_newInf|sanitize}" src="{$imgpath}/help.png" alt="" />
        <a href="javascript: void(0);" onclick="insertEditor('Inhalt','[--NEU--]')">{#Global_newPage#}</a>
      </fieldset>
    </div>
    {if $langcode == 1}
      <div id="opt-2">
        <table width="100%" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td valign="top">
              <fieldset>
                <legend><label for="lgenre">{#Global_Categ#}</label></legend>
                <select  style="width: 150px" class="input" id="lgenre" name="Genre">
                  <option value="">-</option>
                  {foreach from=$genres item=g}
                    <option value="{$g->Id}" {if $article->Genre == $g->Id}selected="selected"{/if}>{$g->Name|sanitize}</option>
                  {/foreach}
                </select>
              </fieldset>
            </td>
            <td>
              <fieldset>
                <legend><label for="lHersteller">{#Manufacturer#}</label></legend>
                <select  style="width: 150px" class="input" id="lHersteller" name="Hersteller">
                  <option value="">-</option>
                  {foreach from=$mf item=m}
                    <option value="{$m->Id}" {if $article->Hersteller == $m->Id}selected="selected"{/if}>{$m->Name|sanitize}</option>
                  {/foreach}
                </select>
              </fieldset>
            </td>
            <td>
              <fieldset>
                <legend><label for="lVertrieb">{#Manufacturer_v#}</label></legend>
                <select  style="width: 150px" class="input" id="lVertrieb" name="Vertrieb">
                  <option value="">-</option>
                  {foreach from=$mf item=m}
                    <option value="{$m->Id}" {if $article->Vertrieb == $m->Id}selected="selected"{/if}>{$m->Name|sanitize}</option>
                  {/foreach}
                </select>
              </fieldset>
            </td>
            <td>
              <fieldset>
                <legend><label for="lPlattform">{#Gaming_plattform#}</label></legend>
                <select  style="width: 150px" class="input" id="lPlattform" name="Plattform">
                  <option value="">-</option>
                  {foreach from=$pf item=m}
                    <option value="{$m->Id}" {if $article->Plattform == $m->Id}selected="selected"{/if}>{$m->Name|sanitize}</option>
                  {/foreach}
                </select>
              </fieldset>
            </td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td valign="top">
              <fieldset>
                <legend><label for="lVeroeffentlichung">{#Gaming_articles_pub#}</label></legend>
                <input name="Veroeffentlichung" type="text" class="input" id="lVeroeffentlichung" style="width: 200px" value="{$article->Veroeffentlichung|sanitize}" />
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend><label for="lPreis">{#Products_price#}</label></legend>
                <input name="Preis" type="text" class="input" id="lPreis" style="width: 100px" value="{$article->Preis|sanitize}" />
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>
                  <label for="lShop">{#Gaming_articles_shop_adress#}</label>
                  <span class="stip" title="{$lang.Gaming_articles_urlInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>
                </legend>
                <input name="Shop" type="text" class="input" id="lShop" style="width: 150px" value="{$article->Shop|sanitize}" />
              </fieldset></td>
            <td valign="top">
              <fieldset>
                <legend>
                  <label for="lShopArtikel">{#Gaming_articles_shop#} <span class="stip" title="{$lang.Gaming_articles_shopInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></label>
                </legend>
                <input name="ShopArtikel" type="text" class="input" id="lShopArtikel" style="width: 150px" value="{$article->ShopArtikel|sanitize}" />
              </fieldset>
            </td>
            <td>
              <fieldset>
                <legend>
                  <label for="lKennwort">{#LoginPass#}</label>
                  <span class="stip" title="{$lang.Global_DocPass|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span>
                </legend>
                <input name="Kennwort" type="text" class="input" id="lKennwort" style="width: 100px" value="{$article->Kennwort}" />
              </fieldset>
            </td>
          </tr>
        </table>
        <table width="100%" border="0" cellpadding="0" cellspacing="2">
          <tr>
            <td>
              <fieldset>
                <legend>
                  <label for="lWertungsDaten">{#Gaming_articles_voting#} {#Gaming_articles_nospecial#}</label>
                  <br />
                </legend>
                <table width="100%" border="0" cellspacing="2" cellpadding="0">
                  <tr>
                    <td width="40%" valign="top"><textarea cols="" rows="" name="WertungsDaten" id="lWertungsDaten" style="width: 98%; height: 110px">{$article->WertungsDaten|sanitize}</textarea></td>
                    <td valign="top">
                      {#Gaming_articles_votingInf1#}
                      <br />
                      <br />
                      <em>{#Gaming_articles_votingInf2#}</em>
                    </td>
                  </tr>
                </table>
              </fieldset>
            </td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td width="50%" valign="top">
              <fieldset>
                <legend>
                  <span class="stip" title="{$lang.Gaming_articles_flotosInf2|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" />&nbsp;</span>
                  <label for="lTop">{#Gaming_articles_tops#} {#Gaming_articles_nospecial#}</label>
                </legend>
                <textarea cols="" rows="" name="Top" id="lTop" style="width: 99%; height: 100px">{$article->Top|sanitize}</textarea>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>
                  <span class="stip" title="{$lang.Gaming_articles_flotosInf2|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" />&nbsp;</span>
                  <label for="lFlop">{#Gaming_articles_flops#} {#Gaming_articles_nospecial#}</label>
                </legend>
                <textarea cols="" rows="" name="Flop" id="lFlop" style="width: 99%; height: 100px">{$article->Flop|sanitize}</textarea>
              </fieldset>
            </td>
          </tr>
        </table>
        <table width="100%" border="0" cellspacing="2" cellpadding="0">
          <tr>
            <td width="50%" valign="top">
              <fieldset>
                <legend><span class="stip" title="{$lang.Gaming_articles_flotosInf2|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" />&nbsp;</span>
                  <label for="lMin">{#Gaming_articles_min#} {#Gaming_articles_nospecial#}</label>
                </legend>
                <textarea cols="" rows="" name="Minimum" id="lMin" style="width: 99%; height: 100px">{$article->Minimum|sanitize}</textarea>
              </fieldset>
            </td>
            <td valign="top">
              <fieldset>
                <legend>
                  <span class="stip" title="{$lang.Gaming_articles_flotosInf2|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" />&nbsp;</span>
                  <label for="lOpt">{#Gaming_articles_opt#} {#Gaming_articles_nospecial#}</label>
                </legend>
                <textarea cols="" rows="" name="Optimum" id="lOpt" style="width: 99%; height: 100px">{$article->Optimum|sanitize}</textarea>
              </fieldset>
            </td>
          </tr>
        </table>
      </div>
    {/if}
    <div id="opt-3">
      <fieldset>
        <legend>{#Image#} </legend>
        <div>
          {if !empty($article->Bild)}
            <img src="../uploads/articles/{$article->Bild}" alt="" />
          {/if}
        </div>
        <label><input type="checkbox" name="NoImg" value="1" />{#Global_ImgDel#}</label>
      </fieldset>
      <fieldset>
        <legend>{#Global_imgNew#} {#Gaming_articles_imgInf#}</legend>
        <div id="UpInf_1"></div>
        <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_1" type="file" size="30" name="fileToUpload_1" class="input" />
        <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('iconupload', 1);" value="{#UploadButton#}" />
        {if perm('mediapool')}
          <input type="button" class="button" onclick="uploadBrowser('image', 'articles', 1);" value="{#Global_ImgSel#}" />
        {/if}
        <input type="hidden" name="Bild_1" id="newFile_1" />
      </fieldset>
      <fieldset>
        <legend>{#Global_imgAlign#}</legend>
        <label><input type="radio" name="Bildausrichtung" value="right" {if $article->Bildausrichtung == 'right' || $smarty.request.Bildausrichtung == 'right' || empty($article->Bildausrichtung)}checked="checked"{/if} /> {#Global_ImgRight#}</label>
        <label><input type="radio" name="Bildausrichtung" value="left" {if $article->Bildausrichtung == 'left' || $smarty.request.Bildausrichtung == 'left'}checked="checked"{/if} /> {#Global_ImgLeft#}</label>
      </fieldset>
    </div>
    <div id="opt-4">
      {if $langcode == 1}
        <fieldset>
          <legend>{#Gaming_articles_istop#}</legend>
          <label>
            <input type="radio" name="Topartikel" value="1" {if $article->Topartikel == 1}checked="checked"{/if}/>{#Yes#}</label>
          <label>
            <input type="radio" name="Topartikel" value="0" {if $article->Topartikel == 0}checked="checked"{/if}/>{#No#}</label>
          <br />
          <br />
          {#Gaming_articles_topInf#}
        </fieldset>
      {else}
        <input name="Topcontent" type="hidden" value="{$article->Topartikel}" />
      {/if}
      <fieldset>
        <legend>{#Image#}</legend>
        <div>
          {if !empty($article->TopartikelBild)}
            <img src="../uploads/articles/{$article->TopartikelBild}" alt="" />
          {/if}
        </div>
        <label><input type="checkbox" name="NoTopnewsImg" value="1" />{#Global_ImgDel#}</label>
      </fieldset>
      <fieldset>
        <legend>{#Global_imgNew#}</legend>
        <div id="UpInf_2"></div>
        <div id="loading_2" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_2" type="text" size="3" name="resizeUpload_2" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_2" type="file" size="30" name="fileToUpload_2" class="input" />
        <input type="button" class="button" id="buttonUpload_2" onclick="fileUpload('iconupload', 2);" value="{#UploadButton#}" />
        {if perm('mediapool')}
          <input type="button" class="button" onclick="uploadBrowser('image', 'articles', 2);" value="{#Global_ImgSel#}" />
        {/if}
        <input type="hidden" name="Bild_2" id="newFile_2" />
      </fieldset>
    </div>
    <div id="opt-5">
      {assign var='inline_table' value='article'}
      {assign var='fieldname' value=$field_inline}
      {include file="$incpath/screenshots/load.tpl"}
    </div>
    {if $langcode == 1}
      <div id="opt-6">
        <fieldset>
          <legend><span class="stip" title="{$lang.Tag_info|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Tags#} </legend>
          <input type="text" class="input" style="width: 99%" name="Tags" value="{$article->Tags}" />
        </fieldset>
        <fieldset>
          <legend><span class="stip" title="{$lang.Gal_incl_info|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Global_mergeGalleries#}</legend>
              {strip}
            <select name="Gallery[]" size="15" multiple class="input" id="select" style="width: 250px">
              <option value="" {if empty($article->Galerien.0)}selected="selected"{/if}> ---------- {#Sys_off#} ---------- </option>
              {foreach from=$Gallery item=ng}
                <optgroup label="{$ng->CategName}"></optgroup>
                {foreach from=$ng->Gals item=g}
                  <option value="{$g->GalId}" {if in_array($g->GalId, $article->Galerien)}selected="selected"{/if}>{$g->GalName}</option>
                {/foreach}
              {/foreach}
            </select>
          {/strip}
        </fieldset>
        <fieldset>
          <legend><label for="lLinks">{#Links#}</label></legend>
            {#Products_links_inf#}
          <br />
          <br />
          <textarea cols="" rows="" name="Links" id="lLinks" style="width: 99%; height: 77px">{$article->Links}</textarea>
        </fieldset>
      </div>
    {/if}
  </div>
  {if $langcode == 1}
    <label><input type="checkbox" name="saveAllLang" value="1" />{#SavDataAllLangs#}</label>
    <br />
  {/if}
  <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
  <input type="hidden" name="langcode" value="{$langcode}" />
</form>
