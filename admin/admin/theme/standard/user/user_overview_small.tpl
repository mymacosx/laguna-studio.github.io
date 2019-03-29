{if perm('users_overview')}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('user_navi', 'user_open', 30, '{$basepath}');
});
//-->
</script>

<div class="header">
  <div id="user_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
  <img class="absmiddle" src="{$imgpath}/user.png" alt="" /> {#Start_NUser#} - <a href="?do=user&amp;sub=showusers">{#Global_ShowAll#} ({$all_user})</a>
</div>
<div id="user_open" class="sysinfos">
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
    {foreach from=$user item=u}
      <tr class="{cycle values='second,first'}">
        <td>
          <a class="colorbox stip" title="{$lang.User_edit|sanitize}" href="index.php?do=user&amp;sub=edituser&amp;user={$u->Id}&amp;noframes=1"><strong>{if empty($u->Vorname)}<em>{#Edit#}</em>{else}{$u->Vorname|truncate: '15': '...'}{/if} {$u->Nachname|sanitize}</strong></a>
          <input type="hidden" name="deldetails[{$u->Id}]" value="{$u->Benutzername|sanitize} ({$u->Email})" /></td>
        <td>{$u->Benutzername|sanitize}</td>
        <td align="right"> {$u->Regdatum|date_format: "%d.%m.%Y"} </td>
      </tr>
    {/foreach}
  </table>
</div>
{/if}