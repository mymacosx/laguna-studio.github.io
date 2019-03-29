<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset={$charset}" />
<title>{#MpTitle#}</title>
{include file="$incpath/header/style.tpl"}
{include file="$incpath/header/jquery.tpl"}
</head>
<body>
<script type="text/javascript">
<!-- //
function startUpload() {
    $('#loading').show();
    $('#start').val('{#Global_Wait#}').prop('disabled', true);
    document.forms['upform'].submit();
}
//-->
</script>
<form action="index.php?do=browser&amp;sub=receive" method="post" enctype="multipart/form-data" name="upform" id="upform" style="display: inline;">
  <div class="header">{#FileUpload#}</div>
  <center>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center" valign="middle" class="boxstandart">
          <table width="100%" border="0" cellpadding="4" cellspacing="1" class="tableborder">
            <tr class="secondrow">
              <td colspan="2" class="tableheader">
                <div class="mediapool_infobox">
                  <strong>{#MpAllowed#}</strong><br />
                  {assign var="count" value=0}
                  {foreach from=$allowed item=f}
                    <small><strong> .{$f}</strong></small>
                    {assign var="count" value=$count+1}
                    {if $count == 15}
                      <br />
                      {assign var="count" value=0}
                    {/if}
                  {/foreach}
                </div>
              </td>
            </tr>
            {section name=files loop=6}
              <tr class="second">
                <td>№{$smarty.section.files.index + 1}&nbsp;</td>
                <td><input class="input" name="upfile[]" type="file" id="upfile[]" size="60" /></td>
              </tr>
            {/section}
            {if $smarty.request.typ == 'image'}
              <tr class="secondrow">
                <td class="second">&nbsp;</td>
                <td class="second">
                  <input name="resize" type="checkbox" value="1" /> {#MpResize#}
                  <input name="w" type="text" value="120" size="3" /> {#MpWidth#}
                </td>
              </tr>
            {/if}
            <tr class="secondrow">
              <td class="firstrow">&nbsp;</td>
              <td>
                <div id="loading" style="display: none;"><img src="{$imgpath}/ajaxbar.gif" alt="" /></div>
                <input name="typ" type="hidden" value="{$smarty.request.typ}" />
                <input name="pfad" type="hidden" value="{$smarty.request.pfad}" />
                <input name="target" type="hidden" value="{$smarty.request.target}" />
                <input name="button" type="button" class="button" onclick="startUpload();" value="{#FileUpload#}" id="start" />
                <input name="button" type="button" class="button" onclick="window.close();" value="{#Close#}" />
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </center>
</form>
</body>
</html>
