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
            Url: { required: true, url: true },
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
//-->
</script>

<form method="post" action="" enctype="multipart/form-data" name="sysform" id="sysform">
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr>
      <td width="50%">
        <fieldset>
          <legend>
            <label for="t">{#Global_Name#}</label>
          </legend>
          <input name="Name" type="text" class="input" id="t" style="width: 180px" value="{$res->Name}" />
        </fieldset>
      </td>
      <td>
        <fieldset>
          <legend>{#Global_PublicDate#}</legend>
          <input name="Datum" id="Datum" type="text" class="input" style="width: 120px" value="{$res->Datum|date_format: '%d.%m.%Y'}" />
        </fieldset>
      </td>
    </tr>
  </table>
  <table width="100%" border="0" cellpadding="1" cellspacing="1">
    <tr>
      <td>
        <fieldset>
          <legend><label for="url">{#Links_Url#}</label></legend>
          <input name="Url" type="text" class="input" id="url" style="width: 180px" value="{$res->Url}" />
        </fieldset>
      </td>
      <td>
        <fieldset>
          <legend><label for="c">{#Global_Categ#}</label></legend>
          <select class="input" style="width: 200px" name="Kategorie">
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
      <td width="130">
        <fieldset>
          <legend>
            <label for="spr">{#Links_ccode#}</label>
            <span class="stip" title="{$lang.Links_ccodeInf|sanitize}"><img class="absmiddle" src="{$imgpath}/help.png" alt="" /></span></legend>
          <input name="Sprache" type="text" class="input" id="spr" style="width: 50px" value="{$res->Sprache|default:'ru'}" maxlength="2" />
        </fieldset>
      </td>
    </tr>
  </table>
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
  <fieldset>
    <legend>{#Content_text#}</legend>
    {$Beschreibung}
  </fieldset>
</div>
<input type="submit" class="button" value="{#Save#}" />
<input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
<input name="save" type="hidden" id="save" value="1" />
<input type="hidden" name="langcode" value="{$smarty.request.langcode|default:1}" />
</form>
