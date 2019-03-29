{if (isset($smarty.request.sentlinks) && $smarty.request.sentlinks == 1) && empty($error)}
  <div class="popup_header">{$sname}</div>
  <table width="100%" cellpadding="4" cellspacing="1" class="box_inner">
    <tr>
      <td width="10%" class="row_first">
        <div align="center">
          {#GlobalSend#}
          <br />
          <br />
          <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
        </div>
      </td>
    </tr>
  </table>
{else}
{script file="$jspath/jvalidate.js" position='head'}
<script type="text/javascript">
<!-- //
{include file="$incpath/other/jsvalidate.tpl"}
$(document).ready(function() {
    $('#cf').validate({
        rules: {
            Beschreibung: { required: true, minlength: 10 },
            Name: { required: true },
            Url: { required: true }
        },
        submitHandler: function() {
            document.forms['fc'].submit();
        },
        success: function(label) {
            label.html('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').addClass('checked');
        }
    });
});
//-->
</script>
{if $loggedin}
{script file="$jspath/jupload.js" position='head'}
<script type="text/javascript">
<!-- //
function fileUpload(sub, divid) {
    $(document).ajaxStart(function() {
        $('UpInf_' + divid).hide();
        $('#loading_' + divid).show();
        $('#buttonUpload_' + divid).val('{#Global_Wait#}').prop('disabled', true);
    }).ajaxComplete(function() {
        $('#loading_' + divid).hide();
        $('#buttonUpload_' + divid).val('{#UploadButton#}').prop('disabled', false);
    });
    $.ajaxFileUpload({
        url: 'index.php?p=links&action=' + sub + '&divid=' + divid,
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
{/if}

<form name="fc" id="cf" action="index.php?p=links&amp;area={$area}&amp;action=links_sent" method="post">
  <div class="popup_header">{$sname}</div>
  <div class="popup_content" style="padding: 5px">
    <div class="popup_box">
      {if !empty($error)}
        <div class="error_box">
          <ul>
            {foreach from=$error item=err}
              <li>{$err}</li>
              {/foreach}
          </ul>
        </div>
      {/if}
      <br />
      {if !empty($categs)}
        <fieldset>
          <legend>{#Global_Categ#}</legend>
          <select style="width: 98%" class="input" name="Kategorie">
            <option value="">{#Global_Select_Categ#}</option>
            {foreach from=$categs item=dd}
              <option {if isset($smarty.request.Kategorie) && $smarty.request.Kategorie == $dd->Id}selected="selected"{/if} value="{$dd->Id}">{$dd->visible_title} </option>
            {/foreach}
          </select>
        </fieldset>
      {/if}
      <fieldset>
        <legend>{#GlobalTitle#}</legend>
        <input name="Name" type="text" class="input" style="width: 98%" value="{$smarty.request.Name|default:''|sanitize}" maxlength="150" />
      </fieldset>
      <fieldset>
        <legend>{#GlobalLinks#}</legend>
        <input name="Url" type="text" class="input" style="width: 98%" value="{$smarty.request.Url|default:''|escape: html}" maxlength="150" />
      </fieldset>
      <fieldset>
        <legend>{#Description#}</legend>
        <textarea name="Beschreibung" cols="" rows="8" class="input" style="width: 98%">{$smarty.request.Beschreibung|default:''|escape: html}</textarea>
      </fieldset>
      {if $loggedin}
        <fieldset>
          <legend>{#GlobalImage#}</legend>
          <div id="UpInf_1"></div>
          <div id="loading_1" style="display: none;"><img src="{$imgpath_page}ajaxbar.gif" alt="" /></div>
          <input id="fileToUpload_1" type="file" size="30" name="fileToUpload_1" class="input" />
          <input type="button" class="button" id="buttonUpload_1" onclick="fileUpload('uploadicon', 1);" value="{#UploadButton#}" />
          <input type="hidden" name="newImg_1" id="newFile_1" />
        </fieldset>
      {/if}
      {include file="$incpath/other/captcha.tpl"}
    </div>
  </div>
  <p align="center">
    <input type="submit" class="button" value="{#ButtonSend#}" />&nbsp;
    <input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" />
    <input name="sentlinks" type="hidden" value="1" />
  </p>
</form>
{/if}
