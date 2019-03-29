{if perm('users_overview') && !empty($onlineAdmin)}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    toggleCookie('admin_navi', 'admin_open', 30, '{$basepath}');
    toggleCookie('online_navi', 'online_open', 30, '{$basepath}');
});
//-->
</script>

<div class="header">
  <div id="admin_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
  <img class="absmiddle" src="{$imgpath}/mods.png" alt="" /> {#Start_OnAdmin#}
</div>
<div id="admin_open" class="sysinfos">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    {foreach from=$onlineAdmin item=online}
      <tr>
        <td>
          <a class="colorbox stip" title="{$lang.User_edit|sanitize}" href="index.php?do=user&amp;sub=edituser&amp;user={$online->Uid}&amp;noframes=1"><strong>{$online->Benutzername}</strong></a>
        </td>
        <td align="right"><a class="colorbox" href="http://www.status-x.ru/webtools/whois/{$online->Ip}/">{$online->Ip}</a></td>
      </tr>
    {/foreach}
  </table>
</div>
{/if}
{if perm('users_overview') && !empty($onlineUser)}
  <div class="header">
    <div id="online_navi" class="navi_toggle"><img class="absmiddle" src="{$imgpath}/toggle.png" alt="" /></div>
    <img class="absmiddle" src="{$imgpath}/users.png" alt="" /> {#Start_OnUser#}
    {if $count > 10 && $smarty.request.sub != 'showall'}
     - <a class="colorbox" href="index.php?do=main&amp;sub=showall&amp;noframes=1">{#Global_ShowAll#} ({$count})</a>
    {/if}
  </div>
  <div id="online_open" class="sysinfos">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
      {foreach from=$onlineUser item=online}
        <tr>
          <td>
            {if $online->Benutzername != 'UNAME'}
              {if $online->Bots == 0}
                <a class="colorbox stip" title="{$lang.User_edit|sanitize}" href="index.php?do=user&amp;sub=edituser&amp;user={$online->Uid}&amp;noframes=1"><strong>{$online->Benutzername}</strong></a>
                  {else}
                <strong>{$online->Benutzername}</strong>
              {/if}
            {else}
              <strong>{#Guest#}</strong>
            {/if}
          </td>
          <td align="right"><a class="colorbox" href="http://www.status-x.ru/webtools/whois/{$online->Ip}/">{$online->Ip}</a></td>
        </tr>
      {/foreach}
    </table>
  </div>
{/if}
