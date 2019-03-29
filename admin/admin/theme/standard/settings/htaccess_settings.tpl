{if $admin_settings.EditArea == 1}
<script type="text/javascript" src="{$baseurl}/lib/edit_area/edit_area_full.js"></script>
{/if}
<script type="text/javascript">
<!-- //
function loadArea() {
    {if $admin_settings.EditArea == 1}
    editAreaLoader.init({
        id: 'htaccess', syntax: 'html', start_highlight: true,
        {if $browser == 'ie9' || $browser == 'ie8' || $browser == 'ie7' || $browser == 'ie6'}
        language: 'en'
        {else}
        language: '{$langcode|default:"ru"}'
        {/if}
    });
    {/if}
}
$(document).ready(function() {
    {if $row.auto == 1}
    {if $save == 1}
    var html = '<h3>{#Global_savemsg#}</h3><iframe id="iframe" frameborder="0" width="1" height="1" src="{$baseurl}"></iframe>';
    $.blockUI({
        showOverlay: true,
        message: html,
        css: { cursor: 'pointer' }
    });
    $('#iframe').load(function() {
        $.unblockUI;
        location.href = location;
    });
    {/if}
    $('#source').hide();
    $('.no_auto').show();
    {else}
    $('.no_auto').hide();
    loadArea();
    {/if}
    $('#auto1, #auto2').on('click', function() {
	if ($(this).attr('id') == 'auto1') {
	    $('#source').hide();
            $('.no_auto').show();
	} else {
	    $('#source').show();
            $('.no_auto').hide();
            loadArea();
	}
    });
});
//-->
</script>

<div class="header">{#HtaccessSettings#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div>
  <form method="post" action="">
    <table width="100%" border="0" cellpadding="4" cellspacing="0">
      <tr>
        <td width="250" class="row_left">{#HtaccessAuto#}</td>
        <td class="row_right">
          <label><input type="radio" name="auto" id="auto1" value="1" {if $row.auto == 1}checked="checked"{/if} /> {#Yes#} </label>
          <label><input type="radio" name="auto" id="auto2" value="0" {if $row.auto == 0}checked="checked"{/if} /> {#No#} </label>
        </td>
      </tr>
      <tr class="no_auto">
        <td class="row_left" colspan="2">&nbsp;</td>
      </tr>
      <tr class="no_auto">
        <td class="row_left">
          {#GlobalUse#} mod_rewrite
          {if !$row.mod_rewrite}
            &nbsp;&nbsp;&nbsp;<img class="absmiddle stip" title="{#HtaccessWarning#}" src="{$imgpath}/warning.gif" alt="" border="0" />
          {/if}
        </td>
        <td class="row_right">
          <label><input type="radio" name="rewrite" value="1" {if $row.rewrite == 1}checked="checked"{/if} /> {#Yes#} </label>
          <label><input type="radio" name="rewrite" value="0" {if $row.rewrite == 0}checked="checked"{/if} /> {#No#} </label>
        </td>
      </tr>
      <tr class="no_auto">
        <td class="row_left">
          {#GlobalUse#} mod_expires
          {if !$row.mod_expires}
            &nbsp;&nbsp;&nbsp;<img class="absmiddle stip" title="{#HtaccessWarning#}" src="{$imgpath}/warning.gif" alt="" border="0" />
          {/if}
        </td>
        <td class="row_right">
          <label><input type="radio" name="expires" value="1" {if $row.expires == 1}checked="checked"{/if} /> {#Yes#} </label>
          <label><input type="radio" name="expires" value="0" {if $row.expires == 0}checked="checked"{/if} /> {#No#} </label>
        </td>
      </tr>
      <tr class="no_auto">
        <td class="row_left">
          {#GlobalUse#} mod_headers
          {if !$row.mod_headers}
            &nbsp;&nbsp;&nbsp;<img class="absmiddle stip" title="{#HtaccessWarning#}" src="{$imgpath}/warning.gif" alt="" border="0" />
          {/if}
        </td>
        <td class="row_right">
          <label><input type="radio" name="headers" value="1" {if $row.headers == 1}checked="checked"{/if} /> {#Yes#} </label>
          <label><input type="radio" name="headers" value="0" {if $row.headers == 0}checked="checked"{/if} /> {#No#} </label>
        </td>
      </tr>
      <tr class="no_auto">
        <td class="row_left" colspan="2">&nbsp;</td>
      </tr>
      <tr class="no_auto">
        <td class="row_left">{#HtaccessLich#}</td>
        <td class="row_right">
          <label><input type="radio" name="lich" value="1" {if $row.lich == 1}checked="checked"{/if} /> {#Yes#} </label>
          <label><input type="radio" name="lich" value="0" {if $row.lich == 0}checked="checked"{/if} /> {#No#} </label>
        </td>
      </tr>
      <tr class="no_auto">
        <td class="row_left">{#HtaccessExts#}</td>
        <td class="row_right">
          <textarea cols="" rows="" class="input" style="width: 100px; height: 70px" onclick="focusArea(this, 140);" name="exts">{$row.exts|sanitize}</textarea>
        </td>
      </tr>
      <tr class="no_auto">
        <td class="row_left" colspan="2">&nbsp;</td>
      </tr>
      <tr class="no_auto">
        <td class="row_left">{#HtaccessWww#}</td>
        <td class="row_right">
          <label><input type="radio" name="www" value="0" {if $row.www == 0}checked="checked"{/if} /> {#No#} </label>
          <label><input type="radio" name="www" value="1" {if $row.www == 1}checked="checked"{/if} /> <a class="colorbox" href="http://www.{$host}">www.{$host}</a> </label>
          <label><input type="radio" name="www" value="2" {if $row.www == 2}checked="checked"{/if} /> <a class="colorbox" href="http://{$host}">{$host}</a> </label>
        </td>
      </tr>
      <tr>
        <td class="row_left" colspan="2">&nbsp;</td>
      </tr>
    </table>
    {if !empty($error)}
      <br />
      <span style="color: red">{$error}</span>
      <br />
      <br />
    {/if}
    <div id="source">
      <textarea cols="" rows="" id="htaccess" class="input" wrap="off" style="width: 98%; height: 450px; font: 12px 'Courier New', Courier, monospace" name="htaccess">{$htaccess|escape: html}</textarea>
    </div>
    <br />
    <input name="save" type="hidden" value="1" />
    <input type="submit" value="{#Save#}" class="button" />
  </form>
</div>
