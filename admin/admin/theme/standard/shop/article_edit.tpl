{assign var='langcode' value=$smarty.request.langcode|default:1}
<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['editform'].submit();
    }
});
$(document).ready(function() {
    $('#edit_form').validate({
        ignore: '#ops',
        rules: {
            Titel: { required: true, minlength: 3 },
            Artikelnummer: { required: true, minlength: 5 },
            Preis_Liste: { required: true, number: true },
            Gewicht: { required: true, number: true, min: 0 },
            Lagerbestand: { required: true, number: true },
            MinBestellung: { required: true, number: true, min: 0 },
            MaxBestellung: { required: true, number: true, min: 0 }
        },
        messages: {
            Preis_Liste: { required: '{#Shop_articles_edit_jsnoprice#}' }
        },
        success: function(label) {
            label.html("&nbsp;").addClass("checked");
        }
    });

    $('#ops').tabs({
        selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	    $('#current_tabs').val(ui.index);
	}
    });

    $('#dateinput').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd.mm.yy',
        dayNamesMin: [{#Calendar_daysmin#}],
        monthNamesShort: [{#Calendar_monthNamesShort#}],
        firstDay: 1
    });
});

function selectBestellt(id) {
    if(id.options[id.selectedIndex].value == 2) {
        document.getElementById('ls').value = '0';
        document.getElementById('Bestellt').checked = true;
        document.getElementById('Bestellt').disabled = false;
    } else {
        document.getElementById('Bestellt').checked = false;
        document.getElementById('Bestellt').disabled = true;
    }
}
function checkBestellt() {
    if(document.getElementById('Bestellt').checked == true) {
        document.getElementById('s_2').selected = true;
        document.getElementById('ls').value = '0';
    }
}
function getValue(id) {
    {foreach from=$units item=u}
    var unit_{$u.Id} = '{$u.Name|sanitize}';
    {/foreach}

    if(id) {
        var out = eval('unit_'+id);
	document.getElementById('bezugelem').innerHTML=out;
    }
}
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
        var up = 'index.php?do=shop&sub=' + sub + '&divid=' + divid + '&resize=' + resize;
    } else {
        var up = 'index.php?do=shop&sub=' + sub + '&divid=' + divid;
    }
    $.ajaxFileUpload({
        url: up,
        secureuri: false,
        fileElementId: 'fileToUpload_' + divid,
        dataType: 'json',
        success: function(data) {
	    if(typeof(data.result) !== 'undefined') {
                document.getElementById('UpInf_' + divid).innerHTML = data.result;
                if(data.filename !== '') {
                    document.getElementById('newFile_' + divid).value = data.filename;
                }
	    }
        },
        error: function(data, status, e) {
            document.getElementById('UpInf_' + divid).innerHTML = e;
        }
    });
    return false;
}
function changeLang(langcode) {
    if(confirm('{#Global_changeLangDoc#}')) {
        location.href='index.php?do=shop&sub=edit_article&id={$smarty.request.id}&noframes=1&langcode=' + langcode;
    } else {
        document.getElementById('l_{$langcode}').selected=true;
    }
}
//-->
</script>

<div class="header_inf">
  <form method="post" action="">
    <select class="input" onchange="eval(this.options[this.selectedIndex].value);" name="langcode" id="langcode">
      <option id="l_1" value="changeLang(1);" {if $langcode == 1}selected="selected"{/if}>{#Shop_articles_editin#} - {$language.name.1|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_2" value="changeLang(2);" {if $langcode == 2}selected="selected"{/if}>{#Shop_articles_editin#} - {$language.name.2|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_3" value="changeLang(3);" {if $langcode == 3}selected="selected"{/if}>{#Shop_articles_editin#} - {$language.name.3|upper} - {#Shop_articles_editinb#}</option>
    </select>
    <img class="absmiddle" src="{$imgpath}/arrow_right.png" alt="" />
  </form>
</div>
<form id="edit_form" name="editform" action="?do=shop&amp;sub=edit_article&amp;id={$smarty.request.id}&amp;langcode={$langcode}&amp;noframes=1&amp;save=1" method="post" enctype="multipart/form-data">
  <div id="ops">
    <ul>
        {if $langcode == 1}
        <li><a href="#main">{#Shop_articles_edit_all#}</a></li>
        {/if}
      <li><a href="#artd">{#Global_descr#}</a></li>
        {if $langcode == 1}
        <li><a href="#pricing_details">{#Shop_articles_edit_pricing#}</a></li>
        <li><a href="#images">{#Shop_articles_edit_images#}</a></li>
        <li><a href="#freefields">{#Shop_freeFields#}</a></li>
        {/if}
      <li><a href="#spez">{#Shop_articles_spez#}</a></li>
        {if $langcode == 1}
        <li><a href="#pdls">{#Shop_pdls#}</a></li>
          {if $tabs->TAB1}
          <li><a href="#accessories">{$tabs->TAB1|sanitize}</a></li>
          {/if}
          {if $tabs->TAB2}
          <li><a href="#parts">{$tabs->TAB2|sanitize}</a></li>
          {/if}
          {if $tabs->TAB3}
          <li><a href="#tuning">{$tabs->TAB3|sanitize}</a></li>
          {/if}
        {/if}
    </ul>
    <div id="main">
      <table width="100%" border="0" cellspacing="0" cellpadding="1">
        {if $langcode == 1}
          <tr>
            <td width="250" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_artInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Global_name#} ({$language.name.$langcode})</td>
            <td class="row_right"><input type="text" class="input" name="Titel" style="width: 350px" value="{$row.Titel}" /></td>
          </tr>
          <tr>
            <td width="180" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_artnrInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_artnr#}</td>
            <td class="row_right"><input type="text" class="input" name="Artikelnummer" style="width: 350px" value="{$row.Artikelnummer}" /></td>
          </tr>
          <tr>
            <td width="180" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_art_matchwordsinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_art_matchwords#}</td>
            <td class="row_right"><input type="text" class="input" name="Schlagwoerter" style="width: 350px" value="{$row.Schlagwoerter}" /></td>
          </tr>
          <tr>
            <td width="180" class="row_left"><img class="absmiddle stip" title="{$lang.ShopAltTitleI|sanitize}" src="{$imgpath}/help.png" alt="" /> {#ShopAltTitle#}</td>
            <td class="row_right"><input type="text" class="input" name="SeitenTitel" style="width: 350px" value="{$row.SeitenTitel|sanitize}" /></td>
          </tr>
          <tr>
            <td width="210" class="row_left"><img class="absmiddle stip" title="{$lang.MetaTagsInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#MetaTags#}</td>
            <td class="row_right"><input type="text" class="input" name="MetaTags" style="width: 350px" value="{$row.MetaTags|sanitize}" /></td>
          </tr>
          <tr>
            <td width="210" class="row_left"><img class="absmiddle stip" title="{$lang.MetagDescInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#MetagDesc#}</td>
            <td class="row_right"><input type="text" class="input" name="MetaDescription" style="width: 350px" value="{$row.MetaDescription|sanitize}" /></td>
          </tr>
          <tr>
            <td width="180" class="row_left"><img class="absmiddle stip" title="{$lang.ShopAltTplDefI|sanitize}" src="{$imgpath}/help.png" alt="" /> {#ShopAltTpl#}</td>
            <td class="row_right">
              <select name="Template">
                <option value="">{#ShopAltTplDef#}</option>
                {foreach from=$alternativeTpl item=at}
                  <option value="{$at->Name}" {if $at->Name == $row.Template}selected="selected"{/if}>{$at->Name}</option>
                {/foreach}
              </select>
            </td>
          </tr>
          <tr>
            <td width="180" class="row_left"><img class="absmiddle stip" title="{$lang.FSK18_ShopProductinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#FSK18_ShopProduct#}</td>
            <td class="row_right">
              <label><input type="radio" name="Fsk18" value="1" {if $row.Fsk18 == 1}checked="checked"{/if} /> {#Yes#}</label>
              <label><input type="radio" name="Fsk18" value="0" {if $row.Fsk18 == 0}checked="checked"{/if} /> {#No#}</label>
            </td>
          </tr>
          <tr>
            <td width="180" valign="top" class="row_left">
              {#Shop_allowed_who#}
              <br />
              <br />
              <small> {#Shop_allowed_whoInf#} </small>
            </td>
            <td class="row_right"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="200" valign="top">
                    <label><input type="checkbox" class="absmiddle" name="AlleGruppen" value="1" {if $groupsempty == 1}checked="checked"{/if}/><strong>{#All_Grupp#}</strong></label>
                    <br />
                    <br />
                    {#Shop_allowed_select#}
                  </td>
                  <td valign="top"><select name="Gruppen[]" size="8" multiple="multiple" class="input" style="width: 250px">
                      {foreach from=$UserGroups item=group}
                        <option value="{$group->Id}" {if in_array($group->Id,$groups)}selected="selected" {/if}>{$group->Name_Intern}</option>
                      {/foreach}
                    </select>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td width="180" class="row_left">{#Sys_on#}</td>
            <td class="row_right">
              <label><input type="radio" name="Aktiv" value="1" {if $row.Aktiv == 1}checked="checked"{/if} /> {#Yes#}</label>
              <label><input type="radio" name="Aktiv" value="0" {if $row.Aktiv == 0}checked="checked"{/if} /> {#No#}</label>
            </td>
          </tr>
          <tr>
            <td width="180" class="row_left">{#Shop_articles_startpage#}</td>
            <td class="row_right">
              <label><input type="radio" name="Startseite" value="1" {if $row.Startseite == 1}checked="checked"{/if} /> {#Yes#}</label>
              <label><input type="radio" name="Startseite" value="0" {if $row.Startseite == 0}checked="checked"{/if} /> {#No#}</label>
            </td>
          </tr>
        {/if}
      </table>
    </div>
    <div id="artd">
      {if $langcode != 1}
        <table width="100%" border="0" cellspacing="0" cellpadding="3">
          <tr>
            <td colspan="2"><h4>{#Global_name#} ({$language.name.$langcode})</h4></td>
          </tr>
          <tr>
            <td width="250" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_artInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Global_name#}</td>
            <td class="row_right"><input type="text" class="input" name="Titel" style="width: 350px" value="{$row.Titel}" /></td>
          </tr>
        </table>
      {/if}
      <table width="100%" border="0" cellspacing="0" cellpadding="3">
        <tr>
          <td>
            <h4>{#Shop_articles_descr#} ({$language.name.$langcode})</h4>
            {$text}
          </td>
        </tr>
        <tr>
          <td>
            <h4>{#Shop_articles_descr2#} ({$language.name.$langcode})</h4>
            {$text2}
            <div style="padding: 4px">
              {include file="$incpath/other/fckinserts.tpl"}
              <img class="absmiddle stip" title="{$lang.Global_newInf|sanitize}" src="{$imgpath}/help.png" alt="" />
              <a href="javascript: void(0);" onclick="insertEditor('Beschreibung2','[--NEU--]');">{#Global_newPage#}</a>
            </div>
          </td>
        </tr>
      </table>
    </div>
    {if $langcode == 1}
      <div id="pricing_details">
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_PriceEkLInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_PriceEkL#}</td>
            <td class="row_right"><input class="input" style="width: 100px" name="Preis_EK" type="text" value="{$row.Preis_EK}" /> {#Option#} </td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_priceinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Products_price#}</td>
            <td class="row_right">
              <input class="input" style="width: 100px" name="Preis_Liste" type="text" id="Preis_Liste" value="{$row.Preis_Liste}" />
              <input name="Preis_Alt" type="hidden" value="{$row.Preis_Liste}" />
              <input name="Angebot_Alt" type="hidden" value="{$row.Preis}" />
            </td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_price_offerinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_price_offer#}</td>
            <td class="row_right"><input class="input" style="width: 100px" name="Preis" type="text" id="Preis" value="{$row.Preis}" /></td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_price_offerinf2|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_price_offertill#}</td>
            <td class="row_right"><input class="input" id="dateinput" name="Preis_Liste_Gueltig" type="text" style="width: 100px" maxlength="10" value="{if $row.Preis_Liste_Gueltig>0}{$row.Preis_Liste_Gueltig|date_format: '%d.%m.%Y'}{/if}" readonly="readonly" /></td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_categInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Global_Categ#}</td>
            <td class="row_right">
              <select class="input" style="width: 200px" name="Kategorie">
                {foreach from=$shop_search_small_categs item=scs}
                  <option {if $scs->bold == 1}class="shop_selector_back"{else}class="shop_selector_subs"{/if} value="{$scs->catid}" {if $row.Kategorie == $scs->catid}selected="selected" {/if}>{$scs->visible_title|specialchars}</option>
                {/foreach}
              </select>
            </td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_categsInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_categs#}</td>
            <td class="row_right">
              <select name="Kategorie_Multi[]" size="8" multiple="multiple" class="input" style="width: 200px">
                {foreach from=$shop_search_small_categs item=scs}
                  <option {if $scs->bold == 1}class="shop_selector_back"{else}class="shop_selector_subs"{/if} value="{$scs->catid}" {if in_array($scs->catid,$row.Kategorie_Multi)}selected="selected" {/if}>{$scs->visible_title|specialchars}</option>
                {/foreach}
              </select>
            </td>
          </tr>
          <tr>
            <td width="270" class="row_left">{#EAN_Code#}</td>
            <td class="row_right"><input class="input" name="EAN_Nr" type="text" style="width: 100px" maxlength="50" value="{$row.EAN_Nr}" /></td>
          </tr>
          <tr>
            <td width="270" class="row_left">{#ISBN_Code#}</td>
            <td class="row_right"><input class="input" name="ISBN_Nr" type="text" style="width: 100px" maxlength="50" value="{$row.ISBN_Nr}" /></td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_art_weightInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_art_weight#}</td>
            <td class="row_right"><input class="input" style="width: 100px" name="Gewicht" type="text" value="{$row.Gewicht}" />&nbsp;{#Shop_articles_art_wunit#}</td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_art_weightRawInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_art_weightRaw#}</td>
            <td class="row_right"><input class="input" style="width: 100px" name="Gewicht_Ohne" type="text" value="{$row.Gewicht_Ohne}" />&nbsp;{#Shop_articles_art_wunit#}</td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_artHBLCInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_art_HBL#}</td>
            <td class="row_right"><input class="input" style="width: 100px" name="Abmessungen" type="text" value="{$row.Abmessungen}" />&nbsp;{#Shop_articles_artHBLC#}</td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_art_manufacturerInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Manufacturer#}</td>
            <td class="row_right">
              <select class="input" style="width: 200px" name="Hersteller" id="Hersteller">
                <option value="0"></option>
                {foreach from=$manufaturer item=m}
                  <option value="{$m->Id}" {if $m->Id == $row.Hersteller}selected="selected"{/if} >{$m->Name}</option>
                {/foreach}
              </select>
            </td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.PrCountryInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#PrCountry#}</td>
            <td class="row_right"><input class="input" style="width: 100px" name="PrCountry" type="text" value="{$row.PrCountry}" /></td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_art_counts_unitinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_art_counts_unit#}</td>
            <td class="row_right">
              <input class="input" style="width: 50px" name="EinheitCount" type="text" value="{$row.EinheitCount|replace: '.': ','}" />
              <select id="gva" onchange="getValue(this.value);" name="EinheitId" class="input" style="width: 143px">
                <option value=""></option>
                {foreach from=$units item=u}
                  <option value="{$u.Id}" {if $u.Id == $row.EinheitId}selected="selected" {/if}>{$u.Name} ({$u.Mz})</option>
                {/foreach}
              </select>
              {#Shop_counts_unit_inc#}
              <input class="input" style="width: 50px" name="EinheitBezug" type="text" value="{$row.EinheitBezug|replace: '.': ','}" />
              <span id="bezugelem" style="font-weight: bold"></span>
<script type="text/javascript">
<!-- //
var xxxx = document.getElementById('gva').options[document.getElementById('gva').selectedIndex].value;
getValue(xxxx);
 //-->
</script>
            </td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_art_onstoreinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_art_onstore#}</td>
            <td class="row_right"><input class="input" style="width: 100px" name="Lagerbestand" id="ls" type="text" value="{$row.Lagerbestand}" /></td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_edit_readyforshippinginf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_edit_readyforshipping#}</td>
            <td class="row_right">
              <select class="input" style="width: 350px" name="Lieferzeit">
                {foreach from=$shipping_time item=st}
                  <option value="{$st->Id}" {if $st->Id == $row.Lieferzeit}selected="selected"{/if} >{$st->Name}</option>
                {/foreach}
              </select>
            </td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_art_availabilityinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_art_availability#}</td>
            <td class="row_right">
              <select class="input" style="width: 350px" onchange="selectBestellt(this);" name="Verfuegbar">
                {foreach from=$available item=av}
                  <option id="s_{$av.Id}" value="{$av.Id}" {if $av.Id == $row.Verfuegbar}selected="selected"{/if} >{$av.Name}</option>
                {/foreach}
              </select>
              <label><input name="Bestellt" type="checkbox" id="Bestellt" value="1" onclick="checkBestellt();" {if $row.Bestellt == 1}checked="checked"{/if} /> {#Shop_articles_edit_isordered#}</label>
              <img class="absmiddle stip" title="{$lang.Shop_articles_edit_isorderedinf|sanitize}" src="{$imgpath}/help.png" alt="" />
            </td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_art_ordermininf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_art_ordermin#}</td>
            <td class="row_right"><input class="input" style="width: 100px" name="MinBestellung" type="text" value="{$row.MinBestellung}" /></td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_art_ordermaxinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_art_ordermax#}</td>
            <td class="row_right"><input class="input" style="width: 100px" name="MaxBestellung" type="text" value="{$row.MaxBestellung}" /></td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.Shop_articles_art_onceorderinf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_articles_art_onceorder#}</td>
            <td class="row_right">
              <label><input type="radio" name="EinzelBestellung" value="1" {if $row.EinzelBestellung == 1}checked="checked"{/if} /> {#Yes#}</label>
              <label><input type="radio" name="EinzelBestellung" value="0" {if $row.EinzelBestellung == 0}checked="checked"{/if} /> {#No#}</label>
            </td>
          </tr>
          <tr>
            <td width="270" class="row_left"><img class="absmiddle stip" title="{$lang.YmlInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Yml#}</td>
            <td class="row_right">
              <label><input type="radio" name="Yml" value="1" {if $row.Yml == 1}checked="checked"{/if} /> {#Yes#}</label>
              <label><input type="radio" name="Yml" value="0" {if $row.Yml == 0}checked="checked"{/if} /> {#No#}</label>
            </td>
          </tr>
        </table>
      </div>
      {assign var=display3 value=none}
      <div id="images">
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
          <tr>
            <td width="180" class="row_left">{#Image#}</td>
            <td class="row_right">
              <input type="hidden" name="Bild_Alt" value="{$row.Bild_Alt}" />
              <img src="{$row.Bild_Norm}" alt="" border="0" />
              <br />
              <label><input type="checkbox" name="Del_Bild_Norm" value="1" /> {#Global_Delete#} </label>
            </td>
          </tr>
          <tr>
            <td width="180" class="row_left">{#Shop_articles_edit_newimage#}</td>
            <td class="row_right">
              <div id="UpInf_1"></div>
              <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
              <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="500" /> px. &nbsp;&nbsp;&nbsp;
              <input id="fileToUpload_1" type="file" size="45" name="fileToUpload_1" class="input" />
              <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('categ_icon', 1);" value="{#UploadButton#}" />
              {if perm('mediapool')}
                <input type="button" class="button" onclick="uploadBrowser('image', 'shop/icons', 1);" value="{#Global_ImgSel#}" />
              {/if}
              <input type="hidden" name="newImg_1" id="newFile_1" />
            </td>
          </tr>
          <tr>
            <td width="180" class="row_left">{#Shop_articles_edit_moreimages#}</td>
            <td class="row_right">
              <table>
                <tr>
                  {if !empty($Bilder)}
                    {assign var=c value=0}
                    {foreach from=$Bilder item=bilder}
                      {assign var=c value=$c+1}
                      <td><img src="{$bilder.Bild}" alt="" border="0" /></td>
                      <td>
                        <label><input type="checkbox" name="Loeschen[]" value="{$bilder.BildId}" /> {#Global_Delete#} </label>
                      </td>
                      {if $c % 4 == 0}
                        {assign var=c value=0}
                      </tr>
                      <tr>
                      {/if}
                    {/foreach}
                  {/if}
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td width="180" class="row_left">{#Shop_articles_edit_moreimages_upload#}</td>
            <td class="row_right">
              {section name=loooo loop=5}
                <input style="margin-bottom: 1px" type="file" name="files[]" />
                <br />
              {/section}
            </td>
          </tr>
        </table>
      </div>
      <div id="freefields">
        <div class="subheaders" style="font-weight: normal">{#Shop_freeFieldsInf#}</div>
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
          <tr>
            <td class="row_left">&nbsp;</td>
            <td class="row_right"><strong>{#Global_Name#}</strong></td>
            <td class="row_right"><strong>{#Shop_freeFields_Must#}</strong></td>
          </tr>
          <tr>
            <td width="180" class="row_left">{#Shop_freeFields_Nr#} #1</td>
            <td width="100" class="row_right"><input class="input" style="width: 200px" name="Frei_1" type="text" value="{$row.Frei_1}" /></td>
            <td class="row_right">
              <label><input type="radio" name="Frei_1_Pflicht" value="1" {if $row.Frei_1_Pflicht == 1}checked="checked"{/if} /> {#Yes#}</label>
              <label><input type="radio" name="Frei_1_Pflicht" value="0" {if $row.Frei_1_Pflicht == 0}checked="checked"{/if} /> {#No#}</label>
            </td>
          </tr>
          <tr>
            <td class="row_left">{#Shop_freeFields_Nr#} #2</td>
            <td class="row_right"><input class="input" style="width: 200px" name="Frei_2" type="text" value="{$row.Frei_2}" /></td>
            <td class="row_right">
              <label><input type="radio" name="Frei_2_Pflicht" value="1" {if $row.Frei_2_Pflicht == 1}checked="checked"{/if} /> {#Yes#}</label>
              <label><input type="radio" name="Frei_2_Pflicht" value="0" {if $row.Frei_2_Pflicht == 0}checked="checked"{/if} /> {#No#}</label>
            </td>
          </tr>
          <tr>
            <td class="row_left">{#Shop_freeFields_Nr#} #3</td>
            <td class="row_right"><input class="input" style="width: 200px" name="Frei_3" type="text" value="{$row.Frei_3}" /></td>
            <td class="row_right">
              <label><input type="radio" name="Frei_3_Pflicht" value="1" {if $row.Frei_3_Pflicht == 1}checked="checked"{/if} /> {#Yes#}</label>
              <label><input type="radio" name="Frei_3_Pflicht" value="0" {if $row.Frei_3_Pflicht == 0}checked="checked"{/if} /> {#No#}</label>
            </td>
          </tr>
        </table>
        <br />
      </div>
      <div id="spez">
        {if isset($nospez) && $nospez == 1}
          <div class="infobox" style="margin: 5px"> {#NoSpezInf#} </div>
        {else}
          {include file="$incpath/shop/specification.tpl"}
        {/if}
      </div>
      <div id="pdls">
        <div class="subheaders">{#Shop_files_descr#}</div>
        <table width="100%" border="0" cellpadding="3" cellspacing="0" class="tableborder">
          <tr>
            <td width="150" class="headers">{#Shop_downloads_hdl#}</td>
            <td width="150" class="headers">{#Shop_downloads_hdln#}</td>
            <td width="250" class="headers">{#Global_descr#}</td>
            <td class="headers">{#Global_Delete#}</td>
          </tr>
          {foreach from=$prodDls item=pdl}
            <tr class="{cycle values='first,second'}">
              <td>
                <select name="Datei[{$pdl->Id}]" style="width: 200px">
                  {foreach from=$prodDlsAll item=pda}
                    <option value="{$pda}" {if $pda == $pdl->Datei}selected="selected"{/if}>{$pda}</option>
                  {/foreach}
                </select>
              </td>
              <td><input type="text" style="width: 180px" name="DlName[{$pdl->Id}]" value="{$pdl->DlName}" /></td>
              <td><input type="text" style="width: 280px" name="DlBeschreibung[{$pdl->Id}]" value="{$pdl->Beschreibung}" /></td>
              <td><input type="checkbox" value="1" name="Dldel[{$pdl->Id}]" /></td>
            </tr>
          {/foreach}
        </table>
        <br />
        <div class="headers">{#UploadButton#}</div>
        <div class="subheaders" style="font-weight: normal">{#Shop_files_newinf#}</div>
        <table width="100%" border="0" cellspacing="0" cellpadding="1">
          <tr>
            <td width="210" class="row_left">{#select_file#}</td>
            <td class="row_right"><select name="DateiDlNeu" style="width: 200px">
                <option value=""></option>
                {foreach from=$prodDlsAll item=pda}
                  <option value="{$pda}">{$pda}</option>
                {/foreach}
              </select>
            </td>
          </tr>
          <tr>
            <td class="row_left">{#UploadNew#}</td>
            <td class="row_right">
              {if $can_upload == 1}
                <div id="UpInf_2"></div>
                <div id="loading_2" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
                <input id="fileToUpload_2" type="file" size="45" name="fileToUpload_2" class="input" />
                <input type="button" class="button" id="buttonUpload_2" onclick="fileUpload('shopfile_upload', 2);" value="{#UploadButton#}" />
                {if perm('mediapool')}
                  <input type="button" class="button" onclick="uploadBrowser('file', 'shop/product_downloads', 2);" value="{#Global_ImgSel#}" />
                {/if}
                <input type="hidden" name="newFile_2" id="newFile_2" />
              {else}
                <strong style="color: red">{#Shop_files_NotWritable#}</strong>
              {/if}
            </td>
          </tr>
          <tr>
            <td width="250" class="row_left">{#Shop_downloads_hdln#}</td>
            <td class="row_right"><input style="width: 200px" type="text" name="DateiName" /></td>
          </tr>
          <tr>
            <td width="250" class="row_left">{#Global_descr#}</td>
            <td class="row_right"><input style="width: 200px" type="text" name="Dateibeschreibung" /></td>
          </tr>
        </table>
        <input type="hidden" name="pdls_update" value="1" />
      </div>
      {if $tabs->TAB1|sanitize}
        {assign var=display5 value=none}
        <div id="accessories">
          <table width="100%" border="0" cellspacing="0" cellpadding="1">
            <tr>
              <td valign="top" class="row_left" width="400"><iframe width="400" frameborder="0" style="height: 450px; width: 400px; border: 0px" src="index.php?noout=1&amp;noframes=1&amp;do=shop&amp;sub=get_prods_categs"></iframe></td>
              <td valign="top" class="row_right">
                &lt;&lt; {#Shop_articles_art_seltcatiframe#}
                <div style="padding-top: 10px">
                  <strong>{#Shop_articles_art_selecte_asse#}</strong>
                  <br />
                  <br />
                  <span id="y">
                    {foreach from=$zub item=z}
                      <input name="prods[]" type="checkbox" value="{$z->Id}" checked="checked" />
                      {$z->Titel}
                      <br />
                    {/foreach}
                  </span>
                </div>
              </td>
            </tr>
          </table>
        </div>
      {/if}
      {if $tabs->TAB2|sanitize}
        {assign var=display6 value=none}
        <div id="parts">
          <table width="100%" border="0" cellspacing="0" cellpadding="1">
            <tr>
              <td style="width: 400px" valign="top" class="row_left" width="400"><iframe width="400" frameborder="0" style="height: 450px; width: 400px; border: 0px" src="index.php?noout=1&amp;noframes=1&amp;do=shop&amp;sub=get_prods_parts"></iframe></td>
              <td valign="top" class="row_right">
                &lt;&lt; {#Shop_articles_art_seltcatiframe#}
                <div style="padding-top: 10px">
                  <strong>{#Shop_articles_art_selecte_asse#}</strong>
                  <br />
                  <br />
                  <span id="ers">
                    {foreach from=$ers item=z}
                      <input name="ersatzteile[]" type="checkbox" value="{$z->Id}" checked="checked" />
                      {$z->Titel}
                      <br />
                    {/foreach}
                  </span>
                </div>
              </td>
            </tr>
          </table>
        </div>
      {/if}
      {if $tabs->TAB3|sanitize}
        {assign var=display7 value=none}
        <div id="tuning">
          <table width="100%" border="0" cellspacing="0" cellpadding="1">
            <tr>
              <td style="width: 400px" valign="top" class="row_left" width="400"><iframe width="400" frameborder="0" style="height: 450px; width: 400px; border: 0px" src="index.php?noout=1&amp;noframes=1&amp;do=shop&amp;sub=get_prods_tuning"></iframe></td>
              <td valign="top" class="row_right">
                &lt;&lt; {#Shop_articles_art_seltcatiframe#}
                <div style="padding-top: 10px">
                  <strong>{#Shop_articles_art_selecte_asse#}</strong>
                  <br />
                  <br />
                  <span id="tun">
                    {foreach from=$tun item=z}
                      <input name="tuningteile[]" type="checkbox" value="{$z->Id}" checked="checked" />
                      {$z->Titel}
                      <br />
                    {/foreach}
                  </span>
                </div>
              </td>
            </tr>
          </table>
        </div>
      {/if}
    {/if}
  </div>
  {if $langcode == 1}
    <strong>{#SavDataAllLangsT#}: </strong>&nbsp;<label><input type="checkbox" name="saveAllLang" value="1" /> {#SavDataAllLangs#}</label>
    {/if}
  <div style="padding-top: 10px">
    <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
    <input type="submit" class="button" value="{#Save#}" />
    <input type="button" class="button" value="{#Close#}" onclick="closeWindow(true);" />
    <input type="hidden" name="closeafter" value="{$smarty.request.closeafter|default:''}" />
  </div>
</form>
