{assign var='langcode' value=$smarty.request.langcode|default:1}
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
    $('#sysform').validate({
        ignore: '#container-options',
        rules: {
            {if $langcode == 1}
            Url: { required: true, url: true },
            {/if}
            Name: { required: true }
        },
        messages: {
            Url: { url: '{#InserURL#}' }
        }
    });
    $('#Datum').datepicker({ changeMonth: true,changeYear: true, dateFormat: 'dd.mm.yy', dayNamesMin: [{#Calendar_daysmin#}], monthNamesShort: [{#Calendar_monthNamesShort#}], firstDay: 1 });

    $('#container-options').tabs({
        selected: {$smarty.post.current_tabs|default:0},
	select: function(event, ui) {
	    $('#current_tabs').val(ui.index);
	}
    });
});
function ajaxSnap() {
    $('#loading_1').show();
    var resize = document.getElementById('resizeUpload_1').value;
    var data = escape(document.getElementById('url').value);
    var options = { target: '#UpInf_1', url: 'index.php?do=links&sub=snapshot&resize=' + resize + '&data=' + data + '&key=' + Math.random(), timeout: 5000,
        success: function() {
            $('#newFile_1').val($('#image').attr('alt'));
            $('#loading_1').hide();
        }
    };
    $('#ajaxSnaps').ajaxSubmit(options);
    return false;
}
function fileUpload(sub, divid) {
    $(document).ajaxStart(function() {
        $('#loading_' + divid).show();
        $('#buttonUpload_' + divid).val('{#Global_Wait#}').prop('disabled', true);
    }).ajaxComplete(function() {
        $('#loading_' + divid).hide();
        $('#buttonUpload_' + divid).val('{#UploadButton#}').prop('disabled', false);
    });
    var resize = document.getElementById('resizeUpload_' + divid).value;
    $.ajaxFileUpload ({
	url: 'index.php?do=links&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
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
        location.href='index.php?do=links&sub=edit&id={$smarty.request.id}&noframes=1&langcode=' + langcode;
    } else {
        document.getElementById('l_{$langcode}').selected=true;
    }
}
//-->
</script>

<div class="header_inf">
  <form onsubmit="" method="post" action="">
    <select class="input" onchange="eval(this.options[this.selectedIndex].value);" name="langcode" id="langcode">
      <option id="l_1" value="changeLang(1);" {if $langcode == 1}selected="selected"{/if}>{#Links_edit_in#} - {$language.name.1|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_2" value="changeLang(2);" {if $langcode == 2}selected="selected"{/if}>{#Links_edit_in#} - {$language.name.2|upper} - {#Shop_articles_editinb#}</option>
      <option id="l_3" value="changeLang(3);" {if $langcode == 3}selected="selected"{/if}>{#Links_edit_in#} - {$language.name.3|upper} - {#Shop_articles_editinb#}</option>
    </select>
    <img class="absmiddle" src="{$imgpath}/arrow_right.png" alt="" />
  </form>
</div>
<form method="post" action="" enctype="multipart/form-data" name="sysform" id="sysform">
  <div id="container-options">
    <ul>
      <li><a href="#opt-1"><span>{#News_tab_gen#}</span></a></li>
            {if $langcode == 1}
        <li><a href="#opt-2"><span>{#Image#}</span></a></li>
              {if $res->DefektGemeldet}
          <li><a href="#opt-3"><span>{#Links_broken#}</span></a></li>
              {/if}
            {/if}
    </ul>
    <div id="opt-1">
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          <td width="50%">
            <fieldset>
              <legend>
                <label for="t">{#Global_Name#} ({$language.name.$langcode})</label>
              </legend>
              <input name="Name" type="text" class="input" id="t" style="width: 300px" value="{$res->Name}" />
            </fieldset>
          </td>
          {if $langcode == 1}
          <td>
            <fieldset>
              <legend>{#Global_PublicDate#}</legend>
              <input name="Datum" id="Datum" type="text" class="input" style="width: 120px" value="{$res->Datum|date_format: '%d.%m.%Y'}" />
            </fieldset>
          </td>
          {/if}
        </tr>
      </table>
      <table width="100%" border="0" cellpadding="1" cellspacing="1">
        <tr>
          {if $langcode == 1}
            <td>
              <fieldset>
                <legend><label for="url">{#Links_Url#}</label></legend>
                <input name="Url" type="text" class="input" id="url" style="width: 300px" value="{$res->Url}" />
              </fieldset>
            </td>
            <td>
              <fieldset>
                <legend><label for="c">{#Global_Categ#}</label></legend>
                <select class="input" style="width: 150px" name="Kategorie">
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
                <label><input type="radio" name="Aktiv" value="1" {if $res->Aktiv == 1}checked="checked"{/if}/>{#Global_online#}</label>
                <label><input type="radio" name="Aktiv" value="0" {if $res->Aktiv == 0}checked="checked"{/if}/>{#Global_offline#}</label>
              </fieldset>
            </td>
            <td width="130">
              <fieldset>
                <legend>
                  <label for="spr">{#Links_ccode#}</label>
                  <span class="stip" title="{$lang.Links_ccodeInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></legend>
                <input name="Sprache" type="text" class="input" id="spr" style="width: 50px" value="{$res->Sprache}" maxlength="2" />
              </fieldset>
            </td>
          {/if}
        </tr>
      </table>
      <fieldset>
        <legend>{#Content_text#} ({$language.name.$langcode})</legend>
        {$Beschreibung}
      </fieldset>
    </div>
    {if $langcode == 1}
      <div id="opt-2">
        <fieldset>
          <legend>{#Image#}</legend>
          <div>{if !empty($res->Bild)}<img src="../uploads/links/{$res->Bild}" alt="" />{/if}</div>
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
            <input type="button" class="button" onclick="uploadBrowser('image', 'links', 1);" value="{#Global_ImgSel#}" />
          {/if}
          <input type="button" class="button" id="ajaxSnaps" onclick="ajaxSnap();" value="{#SnapShotButton#}" />
          <input type="hidden" name="newImg_1" id="newFile_1" />
        </fieldset>
      </div>
      {if $langcode == 1}
        {if $res->DefektGemeldet}
          <div id="opt-3">
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
  </div>
  <input type="hidden" id="current_tabs" name="current_tabs" value="{$smarty.post.current_tabs|default:0}" />
  <input type="submit" class="button" value="{#Save#}" />
  <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
  <input name="save" type="hidden" id="save" value="1" />
  <input type="hidden" name="langcode" value="{$langcode}" />
  {if $langcode == 1}
    <label>
      <input type="checkbox" name="saveAllLang" value="1" />
      {#SavDataAllLangs#}</label>
    {/if}
</form>
