<div class="header">{#SettingsModule#} {#Downloads#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="250" class="row_left">{#Download_settings_comments#}</td>
      <td class="row_right">
        <label><input type="radio" name="Kommentare" value="1" {if $res.Kommentare == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="Kommentare" value="0" {if $res.Kommentare == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Download_settings_rating#}</td>
      <td class="row_right">
        <label><input type="radio" name="Wertung" value="1" {if $res.Wertung == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="Wertung" value="0" {if $res.Wertung == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Download_settings_brokenm#}</td>
      <td class="row_right">
        <label><input type="radio" name="DefektMelden" value="1" {if $res.DefektMelden == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="DefektMelden" value="0" {if $res.DefektMelden == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#Download_settings_showflags#}</td>
      <td class="row_right">
        <label><input type="radio" name="Flaggen" value="1" {if $res.Flaggen == 1}checked="checked"{/if} />{#Yes#}</label>
        <label><input type="radio" name="Flaggen" value="0" {if $res.Flaggen == 0}checked="checked"{/if} />{#No#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#GlobalLimit#}</td>
      <td class="row_right"><input style="width: 70px" class="input" type="text" name="PageLimit" value="{$res.PageLimit}" /></td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
