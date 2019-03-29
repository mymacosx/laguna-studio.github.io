<div class="header">{#Global_Settings#} - {#Admin_Global#}</div>
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
        <td class="row_left"><img class="absmiddle stip" title="{$lang.Ahelp_Inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Ahelp#} </td>
        <td class="row_right">
          <label><input type="radio" name="Ahelp" value="1" {if $row.Ahelp == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="Ahelp" value="0" {if $row.Ahelp == 0}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td class="row_left"><img class="absmiddle stip" title="{$lang.AdminModulInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#AdminModul#}</td>
        <td class="row_right">
          <label><input type="radio" name="Aktiv_Modul" value="1" {if $row.Aktiv_Modul == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="Aktiv_Modul" value="0" {if $row.Aktiv_Modul == 0}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td class="row_left"><img class="absmiddle stip" title="{$lang.AdminNotesInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#AdminNotes#}</td>
        <td class="row_right">
          <label><input type="radio" name="Aktiv_Notes" value="1" {if $row.Aktiv_Notes == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="Aktiv_Notes" value="0" {if $row.Aktiv_Notes == 0}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td class="row_left"><img class="absmiddle stip" title="{$lang.NaviAnimeInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#NaviAnime#}</td>
        <td class="row_right">
          <label><input type="radio" name="Navi_Anime" value="1" {if $row.Navi_Anime == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="Navi_Anime" value="0" {if $row.Navi_Anime == 0}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td class="row_left"><img class="absmiddle stip" title="{$lang.NaviPositionInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#NaviPosition#}</td>
        <td class="row_right">
          <label><input type="radio" name="Navi" value="left" {if $row.Navi == 'left'}checked="checked"{/if} />{#NaviLeft#}</label>
          <label><input type="radio" name="Navi" value="right" {if $row.Navi == 'right'}checked="checked"{/if} />{#NaviRight#}</label>
        </td>
      </tr>
      <tr>
        <td class="row_left"><img class="absmiddle stip" title="{$lang.Editor_text|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Admin_Editor#}</td>
        <td class="row_right">
          <label><input type="radio" name="Type_Redaktor" value="1" {if $row.Type_Redaktor == 1}checked="checked"{/if} />{#EditorCKE#}</label>
          <label><input type="radio" name="Type_Redaktor" value="0" {if $row.Type_Redaktor == 0}checked="checked"{/if} />{#EditorText#}</label>
        </td>
      </tr>
      <tr>
        <td class="row_left"><img class="absmiddle stip" title="{$lang.AdminEditAreaInf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#AdminEditArea#}</td>
        <td class="row_right">
          <label><input type="radio" name="EditArea" value="1" {if $row.EditArea == 1}checked="checked"{/if} />{#Yes#}</label>
          <label><input type="radio" name="EditArea" value="0" {if $row.EditArea == 0}checked="checked"{/if} />{#No#}</label>
        </td>
      </tr>
      <tr>
        <td width="220" class="row_left"><img class="absmiddle stip" title="{$lang.Admin_Login_Ip_Inf|sanitize}" src="{$imgpath}/help.png" alt="" /> {#Admin_Login_Ip#}
          <br />
          <br />
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{#YourIp#} <font color="#FF0000">{$your_ip}</font>
        </td>
        <td class="row_right"><textarea cols="" rows="" class="input" style="width: 400px; height: 70px" onclick="focusArea(this, 140);" name="Login_Ip">{$row.Login_Ip|sanitize}</textarea></td>
      </tr>
    </table>
    <input name="save" type="hidden" id="save" value="1" />
    <input type="submit" value="{#Save#}" class="button" />
  </form>
</div>
