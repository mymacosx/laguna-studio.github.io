{include file="$incpath/forums/user_panel_forums.tpl"}
<div> {$navigation} </div>
<br />
{if $error}
  <div class="error_box"> {$error} </div>
{/if}
<table width="100%" cellpadding="4" cellspacing="1" class="forum_tableborder">
  <tr>
    <td class="forum_header"><strong>{#Forums_ForumTitle_login#}</strong></td>
  </tr>
  <tr>
    <td class="forum_info_main">{#Forums_TitleLockedByPass#}</td>
  </tr>
  <tr>
    <td class="forum_info_main">
      <form action="index.php?p=forum&amp;action=login" method="post">
        <input type="hidden" name="fid" value="{$fid}" />
        <input class="input" type="password" name="pass" />&nbsp;
        <input class="button" type="submit" value="{#Forums_ButtonLogin#}" />
      </form>
    </td>
  </tr>
</table>
{include file="$incpath/forums/forums_footer.tpl"}
