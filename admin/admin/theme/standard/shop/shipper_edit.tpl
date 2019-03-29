{assign var='langcode' value=$smarty.request.lc|default:1}
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
	    Name: { required: true, minlength: 2 },
	    Versanddauer: { required: true, minlength: 1 },
	    'Laender[]': { required: true },
	    'Gruppen[]': { required: true }
	},
	messages: {
	    'Laender[]': { required: '{#Shop_shipper_NoGC#}' },
	    'Gruppen[]': { required: '{#Shop_shipper_NoGI#}' }
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
	url: 'index.php?do=shop&sub=' + sub + '&divid=' + divid + '&resize=' + resize,
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
        location.href='index.php?do=shop&sub=editshipper&Id={$smarty.request.Id}&noframes=1&lc=' + langcode;
    } else {
        document.getElementById('l_{$langcode}').selected=true;
    }
}
//-->
</script>

<div class="popbox">
  <div class="header">{#Shop_shipper_edit#} - {$row->Name|sanitize} ({$language.name.$langcode})</div>
  <div class="header_inf">
    <form method="post" action="">
      <select class="input" onchange="eval(this.options[this.selectedIndex].value);" name="langcode" id="langcode">
        <option id="l_1" value="changeLang(1);" {if $langcode == 1}selected="selected"{/if}>{#Shop_shipper_editin#} - {$language.name.1|upper} - {#Shop_articles_editinb#}</option>
        <option id="l_2" value="changeLang(2);" {if $langcode == 2}selected="selected"{/if}>{#Shop_shipper_editin#} - {$language.name.2|upper} - {#Shop_articles_editinb#}</option>
        <option id="l_3" value="changeLang(3);" {if $langcode == 3}selected="selected"{/if}>{#Shop_shipper_editin#} - {$language.name.3|upper} - {#Shop_articles_editinb#}</option>
      </select>
      <img class="absmiddle" src="{$imgpath}/arrow_right.png" alt="" />
    </form>
  </div>
  <form method="post" action="" name="editForm" id="editForm" enctype="multipart/form-data">
    <table width="100%" border="0" cellpadding="3" cellspacing="0">
      {if $langcode == 1}
        <tr>
          <td class="row_left">{#SavDataAllLangsT#}</td>
          <td class="row_right"><label><input type="checkbox" name="saveAllLang" value="1" />{#SavDataAllLangs#}</label></td>
        </tr>
      {/if}
      <tr>
        <td width="220" class="row_left">{#Global_Name#} ({$language.name.$langcode})</td>
        <td class="row_right"><input style="width: 200px" name="Name" type="text" class="input" id="Name" value="{$row->Name|sanitize}" /></td>
      </tr>
      <tr>
        <td width="220" class="row_left"> {#Global_descr#} ({$language.name.$langcode})</td>
        <td class="row_right">{$intro}</td>
      </tr>
      {if $langcode == 1}
        {if $row->Icon}
          <tr>
            <td width="220" class="row_left">{#Image#}</td>
            <td class="row_right"><img src="../uploads/shop/shipper_icons/{$row->Icon}" alt="" border="0" />
              <input type="hidden" name="IconDelOld" value="{$row->Icon}" />
              <br />
              <label><input type="checkbox" name="IconDel" value="1" />{#Global_ImgDel#}</label>
            </td>
          </tr>
        {/if}
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_shipper_ImgInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Global_imgNew#}</td>
          <td class="row_right">
            {if $writable == 1}
              <div id="UpInf_1"></div>
              <div id="loading_1" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
              <input id="resizeUpload_1" type="text" size="3" name="resizeUpload_1" class="input" value="400" /> px. &nbsp;&nbsp;&nbsp;
              <input id="fileToUpload_1" type="file" size="45" name="fileToUpload_1" class="input" />
              <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('shipper_icons', 1);" value="{#UploadButton#}" />
              {if perm('mediapool')}
                <input type="button" class="button" onclick="uploadBrowser('image', 'shop/shipper_icons', 1);" value="{#Global_ImgSel#}" />
              {/if}
              <input type="hidden" name="newImg_1" id="newFile_1" />
            {else}
              {#Shop_shipper_NW#}
            {/if}
          </td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_shipper_aCInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_payment_aC#} </td>
          <td class="row_right">
            <select name="Laender[]" size="8" multiple class="input" id="Laender" style="width: 200px">
              {foreach from=$countries item=c}
                <option value="{$c.Code}" {if in_array($c.Code,$countries_in)}selected="selected"{/if}>{$c.Name|sanitize}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_shipper_aGInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_payment_aG#} </td>
          <td class="row_right">
            <select name="Gruppen[]" size="8" multiple class="input" id="Gruppen" style="width: 200px">
              {foreach from=$groups item=g}
                <option value="{$g->Id}" {if in_array($g->Id,$groups_in)}selected="selected"{/if}>{$g->Name_Intern|sanitize}</option>
              {/foreach}
            </select>
          </td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_shipper_AifNullInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_shipper_AifNull#}</td>
          <td class="row_right">
            <label><input type="radio" name="GewichtNull" value="0" {if $row->GewichtNull == 0}checked{/if} /> {#Yes#}</label>
            <label><input type="radio" name="GewichtNull" value="1" {if $row->GewichtNull == 1}checked{/if} /> {#No#}</label>
          </td>
        </tr>
        <tr>
          <td class="row_left"><img class="absmiddle stip" title="{$lang.Shop_shipper_PGInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Shop_shipper_PG#} </td>
          <td class="row_right"><input name="Gebuehr_Pauschal" type="text" class="input" style="width: 50px" value="{$row->Gebuehr_Pauschal|sanitize}" maxlength="10" /></td>
        </tr>
        <tr>
          <td class="row_left"> {#Shop_shipper_SD#} </td>
          <td class="row_right"><input name="Versanddauer" type="text" class="input" style="width: 50px" value="{$row->Versanddauer|sanitize}" maxlength="25" /></td>
        </tr>
      {/if}
    </table>
    <input type="submit" name="button" class="button" value="{#Save#}" />
    <input type="button" onclick="closeWindow(true);" class="button" value="{#Close#}" />
    <input name="save" type="hidden" id="save" value="1" />
    <input name="lc" type="hidden" value="{$langcode}" />
  </form>
</div>
