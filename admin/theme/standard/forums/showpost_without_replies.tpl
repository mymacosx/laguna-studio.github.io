{include file="$incpath/forums/user_panel_forums.tpl"}
<div class="box_innerhead">{#Forums_ThreadsEmpty#}</div>
<table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
  <tr>
    <td class="forum_header">{#GlobalMessage#}</td>
    <td align="center" class="forum_header"> {#Forums_Title#}</td>
    <td width="100" align="center" class="forum_header">{#GlobalAutor#}</td>
    <td width="100" align="center" class="forum_header">{#Date#}</td>
  </tr>
  {foreach from=$matches item=post name=post}
    <tr class="{cycle name='f' values='row_small_first,row_small_second'}">
      <td>
        {if $post->message == 'denied'}
          <img class="absmiddle" src="{$imgpath}/statusicons/noperm_locked.png" alt="" />
        {/if}
        {if $post->message == 'denied'}
          <span class="stip" title="{$lang.Forums_LastPostTitleSubjectNoPerm|tooltip}">{$post->topic_title|truncate: 25: '*****'}</span>
        {else}
          <a class="forum_links" href="index.php?p=showtopic&amp;toid={$post->topic_id}&amp;fid={$post->forum_id}&amp;t={$post->topic_title|translit}">{$post->topic_title|sanitize}</a>
        {/if}
      </td>
      <td align="center">
        {if $post->message == 'denied'}
          {$post->forum_title|truncate: 8: '*****'}
        {else}
          <a class="forum_links_small" href="index.php?p=showforum&amp;fid={$post->forum_id}&amp;t={$post->forum_title|translit}">{$post->forum_title|sanitize}</a>
        {/if}
      </td>
      <td align="center"><a href="index.php?p=user&id={$post->Autor}">{$post->Uname|sanitize}</a></td>
      <td align="center">{$post->datum|date_format: $lang.DateFormat}</td>
    </tr>
  {/foreach}
</table>
<br />
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <div align="right">
        {if !empty($pages)}
          {$pages}
        {/if}
        <br />
        <form method="post" action="index.php">
          <select class="input" name="pp">
            {section name=pps loop=76 step=5 start=15}
              <option value="{$smarty.section.pps.index}" {if isset($smarty.request.pp) && $smarty.request.pp == $smarty.section.pps.index}selected="selected"{/if}> {$smarty.section.pps.index} {#eachpage#} </option>
            {/section}
          </select>&nbsp;
          <input type="submit" class="button" value="{#GlobalShow#}" />
          <input type="hidden" name="p" value="forum" />
          <input type="hidden" name="action" value="print" />
          <input type="hidden" name="what" value="topicsempty" />
        </form>
      </div>
    </td>
  </tr>
</table>
<br />
{include file="$incpath/forums/forums_footer.tpl"}
