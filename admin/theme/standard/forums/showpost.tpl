{include file="$incpath/forums/user_panel_forums.tpl"}
<div class="box_innerhead"><strong> {#Forums_ShowPostUser#} &bdquo;{$user_name|sanitize}&ldquo;</strong> ({$post_count})</div>
<table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
  {foreach from=$matches item=post name=post}
    <tr>
      <td class="forum_header">{#GlobalMessage#}</td>
      <td align="center" class="forum_header"> {#Forums_Title#}</td>
      <td width="100" align="center" class="forum_header">{#Date#}</td>
    </tr>
    <tr class="row_small_first">
      <td>
        {if $post->message == 'denied'}
          <img class="absmiddle" src="{$imgpath}/statusicons/noperm_locked.png" alt="" />
        {/if}
        {if $post->message == 'denied'}
          <span class="stip" title="{$lang.Forums_LastPostTitleSubjectNoPerm|tooltip}">{$post->topic_title|truncate: 25: '*****'}</span>
        {else}
          {if !empty($post->title)}
            <strong> {$post->title|sanitize} </strong>
            <br />
          {/if}
          <!--START_NO_REWRITE-->
          {$post->message}
          <!--END_NO_REWRITE-->
        {/if}
      </td>
      <td align="center" valign="top">
        {if $post->message == 'denied'}
          {$post->forum_title|truncate: 8: '*****'}
        {else}
          <a class="forum_links_small" href="index.php?p=showforum&amp;fid={$post->forum_id}&amp;t={$post->forum_title|translit}">{$post->forum_title|sanitize}</a>
        {/if}
      </td>
      <td align="center" valign="top">{$post->datum|date_format: $lang.DateFormat}</td>
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
          <input type="hidden" name="what" value="posting" />
          <input type="hidden" name="id" value="{$smarty.request.id}" />
        </form>
      </div>
    </td>
  </tr>
</table>
<br />
{include file="$incpath/forums/forums_footer.tpl"}
