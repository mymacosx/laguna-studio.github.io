<script type="text/javascript" src="{$jspath}/jupload.js"></script>
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$.validator.setDefaults({
    submitHandler: function() {
        document.forms['Form'].submit();
    }
});
$(document).ready(function() {
    $('#Form').validate({
        ignore: '#container-options',
        rules: {
	    {if isset($smarty.request.langcode) && $smarty.request.langcode == 1}
            Shopurl: { url: true },
            {/if}
	    Titel: { required: true }
	},
        messages: { }
    });
   $('#container-options').tabs({
        selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	   $('#current_tabs').val(ui.index);
	}
    });

    $('#lDatum_Veroffentlichung').datepicker({
	changeMonth: true,
	changeYear: true,
	dateFormat: 'dd.mm.yy',
	dayNamesMin: [{#Calendar_daysmin#}],
	monthNamesShort: [{#Calendar_monthNamesShort#}],
	firstDay: 1
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
	url: 'index.php?do=products&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
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

<form method="post" action="" enctype="multipart/form-data" name="Form" id="Form">
  <div id="container-options">
    <ul>
      <li><a href="#opt-1"><span>{#News_tab_gen#}</span></a></li>
      <li><a href="#opt-3"><span>{#Image#}</span></a></li>
      <li><a href="#opt-5"><span>{#Global_Inline#}</span></a></li>
      <li><a href="#opt-6"><span>{#News_tab_other#}</span></a></li>
    </ul>
    <div id="opt-1">
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td>
            <fieldset>
              <legend><label for="t">{#Global_Name#}</label></legend>
              <input name="Titel" type="text" class="input" id="t" style="width: 200px" value="{$content->Titel}" />
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend><label for="lgenre">{#Global_Categ#}</label></legend>
              <select  style="width: 150px" class="input" id="lgenre" name="Genre">
                <option value="">-</option>
                {foreach from=$genres item=g}
                  <option value="{$g->Id}" {if $content->Genre == $g->Id}selected="selected"{/if}>{$g->Name|sanitize}</option>
                {/foreach}
              </select>
            </fieldset>
          </td>
          <td align="center">
            <fieldset>
              <legend><label for="lDatum_Veroffentlichung">{#Products_pubdate#}</label></legend>
              <input name="Datum_Veroffentlichung" type="text" class="input" id="lDatum_Veroffentlichung" style="width: 100px" value="{$content->Datum_Veroffentlichung|date_format: "%d.%m.%Y"}" />
            </fieldset></td>
          <td align="center">
            <fieldset>
              <legend>{#Global_Status#}</legend>
              <label><input type="radio" name="Aktiv" value="1" checked="checked" />{#Global_online#}</label>
              <label><input type="radio" name="Aktiv" value="0" />{#Global_offline#}</label>
            </fieldset>
          </td>
          <td align="center">
            <fieldset>
              <legend>{#GlobalTops#}</legend>
              <label><input type="radio" name="TopProduct" value="1" />{#Yes#}</label>
              <label><input type="radio" name="TopProduct" value="0" checked="checked" />{#No#}</label>
            </fieldset>
          </td>
        </tr>
      </table>
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td>
            <fieldset>
              <legend><label for="lHersteller">{#Manufacturer#}</label></legend>
              <select  style="width: 150px" class="input" id="lHersteller" name="Hersteller">
                <option value="">-</option>
                {foreach from=$mf item=m}
                  <option value="{$m->Id}" {if $content->Hersteller == $m->Id}selected="selected"{/if}>{$m->Name|sanitize}</option>
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
                  <option value="{$m->Id}" {if $content->Vertrieb == $m->Id}selected="selected"{/if}>{$m->Name|sanitize}</option>
                {/foreach}
              </select>
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend><label for="lPreis">{#Products_price#}</label></legend>
              <input name="Preis" type="text" class="input" id="lPreis" style="width: 100px" value="{$content->Preis}" />
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend><label for="lShopurl">{#Products_shopurl#}</label></legend>
              <input name="Shopurl" type="text" class="input" id="lShopurl" style="width: 150px" value="{$content->Shopurl}" />
            </fieldset>
          </td>
          <td>
            <fieldset>
              <legend><label for="lShop">{#Products_shopname#}</label></legend>
              <input name="Shop" type="text" class="input" id="lShop" style="width: 150px" value="{$content->Shop}" />
            </fieldset>
          </td>
        </tr>
      </table>
      <fieldset>
        <legend>{#Content_text#}</legend>
        {$Content}
        <div style="padding: 4px"><span class="stip" title="{$lang.Global_newInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span><a href="javascript: void(0);" onclick="insertEditor('Content','[--NEU--]')">{#Global_newPage#}</a></div>
      </fieldset>
    </div>
    <div id="opt-3">
      <fieldset>
        <legend>{#Global_imgNew#}</legend>
        <div id="UpInf_1"></div>
        <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
        <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
        <input id="fileToUpload_1" type="file" size="20" name="fileToUpload_1" class="input" />
        <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('iconupload', 1);" value="{#UploadButton#}" />
        {if perm('mediapool')}
          <input type="button" class="button" onclick="uploadBrowser('image', 'products', 1);" value="{#Global_ImgSel#}" />
        {/if}
        <input type="hidden" name="newImg_1" id="newFile_1" />
      </fieldset>
    </div>
    <div id="opt-5">
      {assign var='inline_table' value='products'}
      {assign var='fieldname' value=$field_inline}
      {include file="$incpath/screenshots/load.tpl"}
    </div>
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
        <legend><span class="stip" title="{$lang.Gal_incl_info|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span> {#Global_mergeGalleries#}</legend>
            {strip}
          <select name="Gallery[]" size="10" multiple  class="input" id="select" style="width: 250px">
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
      <fieldset>
        <legend>{#Links#}</legend>
        {#Products_links_inf#}
        <br />
        <br />
        <textarea cols="" rows="" name="Links" style="width: 99%; height: 77px">{$content->Links}</textarea>
      </fieldset>
    </div>
  </div>
  <br />
  <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
  <input type="hidden" name="langcode" value="{$smarty.request.langcode|default:1}" />
</form>
</div>
