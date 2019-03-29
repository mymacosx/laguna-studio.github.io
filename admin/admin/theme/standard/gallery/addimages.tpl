<script type="text/javascript" src="{$jspath}/jform.js"></script>
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('#autoform').submit(function() {
        showNotice('<h2>{$lang.UploadWait}</h2>', 2000);
        var options = { target: '#autooutput', timeout: 3000 };
        $(this).ajaxSubmit(options);
        return false;
    });

    $('#manuform').submit(function() {
        showNotice('<h2>{$lang.UploadWait}</h2>', 2000);
        var options = {
            target: '#manuoutput',
            timeout: 3000,
            success: function() {
            {section name=upload loop=3}
            document.getElementById('t_{$smarty.section.upload.index}').value = '';
            document.getElementById('n_{$smarty.section.upload.index}').value = '';
            document.getElementById('upe_{$smarty.section.upload.index}').value = '';
            {/section}
            }
        };
        $(this).ajaxSubmit(options);
        document.getElementById('manuoutput').innerHTML = '<img src="{$imgpath}/ajaxbar.gif" />';
        return false;
    });
});
//-->
</script>

<div class="popbox">
  <div class="popheaders">
    <h4>{#Gallery_upAuto#}</h4>
    {#Gallery_upAutoInf#}
  </div>
  <div id="autooutput"></div>
  <div id="upcontent"></div>
  <form method="post" id="autoform" action="" onsubmit="document.getElementById('upcontent').style.display = 'none';">
    <table width="100%" border="0" cellpadding="3" cellspacing="0">
      <tr>
        <td width="160" class="row_left"><label for="srcf">{#Gallery_upSource#}</label></td>
        <td class="row_right"><select class="input" style="width: 200px" id="srcf" name="source"> {$galfolders}</select></td>
      </tr>
      <tr>
        <td class="row_left">{#Gallery_upTarget#}</td>
        <td class="row_right">
          <select class="input" style="width: 200px" name="thegal">
            {foreach from=$gallery item=item}
              {if $item->Parent_Id == 0}
                <option value="{$item->Id}" {if $smarty.request.id == $item->Id}selected="selected"{/if}>{$item->visible_title}</option>
              {else}
                <option value="{$item->Id}" {if $smarty.request.id == $item->Id}selected="selected"{/if}>{$item->visible_title}</option>
              {/if}
            {/foreach}
          </select>
        </td>
      </tr>
    </table>
    <br />
    <input type="submit" class="button" value="{#Gallery_upButton#}" />
    <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
    <input name="autosave" type="hidden" value="1" />
  </form>
  <br />
  <br />
  <div class="popheaders">
    <h4>{#Gallery_manUpload#}</h4>
    {#Gallery_manUploadInf#}
    <br />
    {$lang.Gallery_warnBigSize|replace: '__MAXMB__': $post_maxMb}
  </div>
  <div class="row_right" id="manuoutput"></div>
  <form method="post" id="manuform" action="" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="{$post_max}" />
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
      {section name=upper loop=3}
        <tr>
          <td width="250" class="row_left"> {#select_file#} </td>
          <td class="row_right">
            <input name="fileToUpload[]" type="file" class="input" id="upe_{$smarty.section.upper.index}" size="20" />&nbsp;
            <label>{#Global_Name#} <input id="n_{$smarty.section.upper.index}" class="input" type="text" name="Name[]" /></label>
            <label>{#Global_descr#} <input id="t_{$smarty.section.upper.index}" class="input" style="width: 250px" type="text" name="Beschreibung[]" /></label>
          </td>
        </tr>
      {/section}
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td width="250" class="row_left">{#Gallery_upTarget#}</td>
        <td class="row_right">
          <select class="input" style="width: 200px" name="thegal">
            {foreach from=$gallery item=item}
              <option value="{$item->Id}" {if $smarty.request.id == $item->Id}selected="selected"{/if}>{$item->visible_title}</option>
            {/foreach}
          </select>
        </td>
      </tr>
    </table>
    <br />
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td width="250" class="row_left">{#GalleryMaxSize#}</td>
        <td class="row_right"><input type="checkbox" class="absmiddle" name="resize" value="1" checked="checked" /></td>
      </tr>
      <tr>
        <td width="250" class="row_left">{#GalleryMaxSizePx#}</td>
        <td class="row_right"><input style="width: 60px" name="newsize" type="text" value="1280" /> px</td>
      </tr>
    </table>
    <br />
    <input type="submit" class="button" value="{#Gallery_buttonMan#}" />
    <input type="button" onclick="closeWindow();" class="button" value="{#Close#}" />
    <input name="manusave" type="hidden" value="1" />
  </form>
</div>
