<div class="header">{#SettingsModule#} RSS</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form action="" name="sysform" id="sysform" method="post">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="row_left" width="350">{#RssType#} - {#Global_All#}</td>
      <td class="row_right">
        <label><input type="radio" name="all_typ" value="1" {if $row.all_typ == 1}checked="checked"{/if} />{#GlobalFull#}</label>
        <label><input type="radio" name="all_typ" value="0" {if $row.all_typ == 0}checked="checked"{/if} />{#GlobalShort#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#RssCount#} - {#Global_All#}</td>
      <td class="row_right"><label><input class="input" name="all" type="text" style="width: 40px" value="{$row.all}" maxlength="2" /></label></td>
    </tr>
    <tr>
      <td class="row_left" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td class="row_left" width="350">{#RssType#} - {#News#}</td>
      <td class="row_right">
        <label><input type="radio" name="news_typ" value="1" {if $row.news_typ == 1}checked="checked"{/if} />{#GlobalFull#}</label>
        <label><input type="radio" name="news_typ" value="0" {if $row.news_typ == 0}checked="checked"{/if} />{#GlobalShort#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#RssCount#} - {#News#}</td>
      <td class="row_right"><label><input class="input" name="news" type="text" style="width: 40px" value="{$row.news}" maxlength="2" /></label></td>
    </tr>
    <tr>
      <td class="row_left" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td class="row_left" width="350">{#RssType#} - {#Articles#}</td>
      <td class="row_right">
        <label><input type="radio" name="articles_typ" value="1" {if $row.articles_typ == 1}checked="checked"{/if} />{#GlobalFull#}</label>
        <label><input type="radio" name="articles_typ" value="0" {if $row.articles_typ == 0}checked="checked"{/if} />{#GlobalShort#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#RssCount#} - {#Articles#}</td>
      <td class="row_right"><label><input class="input" name="articles" type="text" style="width: 40px" value="{$row.articles}" maxlength="2" /></label></td>
    </tr>
    <tr>
      <td class="row_left" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td class="row_left" width="350">{#RssType#} - {#Forums_nt#}</td>
      <td class="row_right">
        <label><input type="radio" name="forum_typ" value="1" {if $row.forum_typ == 1}checked="checked"{/if} />{#GlobalFull#}</label>
        <label><input type="radio" name="forum_typ" value="0" {if $row.forum_typ == 0}checked="checked"{/if} />{#GlobalShort#}</label>
      </td>
    </tr>
    <tr>
      <td class="row_left">{#RssCount#} - {#Forums_nt#}</td>
      <td class="row_right"><label><input class="input" name="forum" type="text" style="width: 40px" value="{$row.forum}" maxlength="2" /></label></td>
    </tr>
  </table>
  <input name="save" type="hidden" id="save" value="1" />
  <input type="submit" class="button" value="{#Save#}" />
</form>
