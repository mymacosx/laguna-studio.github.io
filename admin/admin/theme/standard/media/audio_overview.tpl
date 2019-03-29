<div class="header">{#Audios#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<div class="subheaders">
  <form method="post" action="index.php?do=media&amp;sub=audio_overview">
    <table width="100%" border="0" cellspacing="0" cellpadding="1">
      <tr>
        <td width="100"><label for="qs">{#Search#}</label></td>
        <td><input style="width: 200px" type="text" class="input" name="q" id="qs" value="{$smarty.request.q|sanitize|replace: 'empty': ''}" /></td>
      </tr>
      <tr>
        <td width="100"><label for="dr">{#DataRecords#}</label></td>
        <td>
          <input class="input" style="width: 50px" type="text" name="pp" id="dr" value="{$limit}" />
          <label></label>
          <input type="submit" class="button" value="{#Search#}" />
        </td>
      </tr>
    </table>
    <label for="dr"></label>
  </form>
</div>
<form method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
    <tr class="headers">
      <td class="headers"><a href="index.php?do=media&amp;sub=audio_overview&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$namesort|default:'name_desc'}&amp;broken={$smarty.request.broken}&amp;pp={$limit}">{#Global_Name#}</a></td>
      <td width="50" align="center" nowrap="nowrap" class="headers">{#GlobalWidth#}</td>
      <td width="65" align="center" nowrap="nowrap" class="headers">{#Tags#}</td>
      <td width="100" align="center" nowrap="nowrap" class="headers"><a href="index.php?do=media&amp;sub=audio_overview&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$datesort|default:'date_desc'}&amp;broken={$smarty.request.broken}&amp;pp={$limit}">{#Global_Date#}</a></td>
      <td width="50" align="center" class="headers"><a href="index.php?do=media&amp;sub=audio_overview&amp;q={if isset($smarty.request.q)}{$smarty.request.q}{/if}&amp;page={$smarty.request.page|default:1}&amp;sort={$usersort|default:'user_desc'}&amp;broken={$smarty.request.broken}&amp;pp={$limit}">{#Global_Author#}</a></td>
      <td width="1" align="center" class="headers"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></td>
      <td width="60" align="center" class="headers">{#Global_Actions#}</td>
    </tr>
    {foreach from=$audios item=g}
      <tr class="{cycle values='second,first'}">
        <td><input class="input" style="width: 350px" name="Name[{$g->Id}]" value="{$g->Name|sanitize}" /></td>
        <td align="center"><input class="input" style="width: 50px" name="Width[{$g->Id}]" value="{$g->Width}" /></td>
        <td align="center" nowrap="nowrap"><input class="input" disabled="disabled" style="width: 65px" type="text" value="[AUDIO:{$g->Id}]" /></td>
        <td width="100" align="center" nowrap="nowrap">{$g->Datum|date_format: $lang.DateFormat}</td>
        <td align="center" nowrap="nowrap"><a class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$g->Benutzer}&amp;noframes=1">{$g->BenutzerName}</a></td>
        <td align="center"><input class="stip" title="{$lang.DelInfChekbox|sanitize}" name="del[{$g->Id}]" type="checkbox" value="1" /></td>
        <td width="60" align="center"><a class="colorbox stip" title="{$lang.Edit|sanitize}" href="index.php?do=media&amp;sub=audio_view&amp;id={$g->Id}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/edit.png" alt="" border="0" /></a></td>
      </tr>
    {/foreach}
  </table>
  <input class="button" type="submit" value="{#Save#}" />
  <input name="save" type="hidden" id="save" value="1" />
</form>
<br />
{if !empty($Navi)}
  <div class="navi_div"> {$Navi} </div>
{/if}
