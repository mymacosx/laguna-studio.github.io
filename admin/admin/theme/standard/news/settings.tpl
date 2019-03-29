<div class="header">{#SettingsModule#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="row_left" width="350">{#GlobalImageSize#}</td>
      <td class="row_right"><input class="input" name="size" type="text" style="width: 40px" value="{$row.size}" maxlength="3" /> px</td>
    </tr>
    <tr>
      <td class="row_left">{#GlobalImageCompres#}</td>
      <td class="row_right"><input class="input" name="compres" type="text" style="width: 40px" value="{$row.compres}" maxlength="2" /> %</td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
