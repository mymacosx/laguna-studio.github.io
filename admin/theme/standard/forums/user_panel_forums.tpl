<table width="100%" cellpadding="4" cellspacing="1" class="forum_tableborder">
  <tr>
    <td class="forum_info_meta padding5">
      {if !$loggedin}
          {$welcome}, {#Forums_Welcome_Guest#}
      {else}
          {$welcome}, <strong>{$smarty.session.user_name}</strong>!
          <br />
          {#Forums_Welcome2#}
      {/if}
    </td>
    <td width="10%" nowrap="nowrap" class="forum_frame">
      <div align="right">
        {if $loggedin}
            {include file="$incpath/forums/userpanel_forums.tpl"}
        {else}
            {include file="$incpath/forums/loginform_forums.tpl"}
        {/if}
      </div>
    </td>
  </tr>
</table>
<br />
{include file="$incpath/forums/header_sthreads.tpl"}

