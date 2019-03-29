{include file="$incpath/forums/user_panel_forums.tpl"}
<p> {$navigation} </p>
<table width="100%" cellpadding="4" cellspacing="1" class="forum_tableborder">
  <tr>
    <td class="forum_header_bolder">{#Forums_Label_choose_type#}</td>
  </tr>
  <tr>
    <td class="forum_info_main">
      <form action="index.php?p=forum&amp;action=save_type" method="post">
        <input type="hidden" name="t_id" value="{$smarty.request.id}" />
        <input type="hidden" name="f_id" value="{$smarty.request.fid}" />
        <select class="input" name="type">
          <option value="0" {if $topic->type == 0}selected{/if}>{#Forums_TypeNorm#}</option>
          <option value="1" {if $topic->type == 1}selected{/if}>{#Forums_Sticky#}</option>
          <option value="100" {if $topic->type == 100}selected{/if}>{#Forums_Announcement#}</option>
        </select>&nbsp;
        <input type="submit" class="button" value="{#Save#}" />&nbsp;
        <input type="button" class="button" value="{#Forums_JumpBack#}" onclick="history.go(-1);" />
      </form>
    </td>
  </tr>
</table>
<br />
{include file="$incpath/forums/forums_footer.tpl"}
