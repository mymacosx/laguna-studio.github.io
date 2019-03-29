<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.user_pop').colorbox({ height: '600px', width: '550px', iframe: true });
});
//-->
</script>

<div><a class="menu_link" href="index.php?p=user&amp;id={$theuid}&amp;area={$area}"><img src="{$imgpath_forums}users_small.png" alt="" /> {#Forums_ShowUserProfile#}</a></div>
{if $loggedin}
<div><a class="menu_link" href="index.php?p=pn&amp;action=new&amp;to={$theuname}"><img src="{$imgpath_forums}pn_small.png" alt="" /> {#Forums_SendPnUser_Link#}</a></div>
{if $send_email == 1}
<div><a class="menu_link user_pop" href="index.php?p=misc&amp;do=email&amp;uid={$theuid}"><img src="{$imgpath_forums}mailbox_small.png" alt="" /> {#SendEmail_Inf#}</a></div>
{/if}
{/if}
<div><a class="menu_link" href="index.php?p=forum&amp;action=print&amp;what=posting&amp;id={$theuid}"><img src="{$imgpath_forums}small_ownposts.png" alt="" /> {#Forums_ShowpostsUser_Link#}</a></div>
{if !isset($smarty.request.action) || $smarty.request.action != 'ignorelist'}
{if $ignore_options == 1}
{if $is_ignore == 1}
<div><a class="menu_link" href="index.php?p=forum&amp;action=ignorelist&amp;sub=del&amp;id={$theuid}"><img src="{$imgpath_forums}ignore_small.png" alt="" /> {#Ignorelist_Del#}</a></div>
{else}
<div><a class="menu_link" href="index.php?p=forum&amp;action=ignorelist&amp;sub=add&amp;id={$theuid}"><img src="{$imgpath_forums}ignore_small.png" alt="" /> {#Ignorelist_Add#}</a></div>
{/if}
{/if}
{/if}
