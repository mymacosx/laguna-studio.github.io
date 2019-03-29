{include file="$incpath/forums/user_panel_forums.tpl"}
<div class="box_innerhead">{#Forums_LastPostsT#}</div>
<table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
  <tr>
    <td class="forum_header">{#GlobalMessage#}</td>
    <td width="200" align="center" class="forum_header">{#GlobalTheme#}</td>
    <td width="120" align="center" class="forum_header">{#Forums_Title#}</td>
    <td width="100" align="center" class="forum_header">{#GlobalAutor#}</td>
    <td width="100" align="center" class="forum_header">{#Date#}</td>
  </tr>
  {foreach from=$matches item=post name=post}
    <tr class="{cycle name='f' values='row_small_first,row_small_second'}">
      <td>
        {if $post->message == 'denied'}
          <img class="absmiddle" src="{$imgpath}/statusicons/noperm_locked.png" alt="" />
          <span class="stip" title="{$lang.Forums_LastPostTitleSubjectNoPerm|tooltip}">{$post->post_title|truncate: 30: '*****'}</span>
        {else}
          <a class="forum_links" href="{$post->PostLink}">{$post->post_title|sanitize}</a>
        {/if}
      </td>
      <td width="200" align="center">
        {if $post->message == 'denied'}
          {$post->topic_title|truncate: 10: '*****'}
        {else}
          <a class="forum_links_small" href="index.php?p=showtopic&amp;toid={$post->topic_id}&amp;fid={$post->forum_id}&amp;t={$post->topic_title|translit}">{$post->topic_title|sanitize}</a>
        {/if}
      </td>
      <td width="120" align="center">
        {if $post->message == 'denied'}
          {$post->forum_title|truncate: 10: '*****'}
        {else}
          <a class="forum_links_small" href="index.php?p=showforum&amp;fid={$post->forum_id}&amp;t={$post->forum_title|translit}">{$post->forum_title|sanitize}</a>
        {/if}
      </td>
      <td width="100" align="center"><a href="index.php?p=user&amp;id={$post->Autor}&amp;area={$area}">{$post->Uname|sanitize}</a></td>
      <td width="100" align="center">{$post->datum|date_format: $lang.DateFormat}</td>
    </tr>
  {/foreach}
</table>
<br />
<br />
{include file="$incpath/forums/forums_footer.tpl"}
