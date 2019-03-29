{include file="$incpath/forums/user_panel_forums.tpl"}
{script file="$jspath/jrating.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.user_pop').colorbox({ height: "450px", width: "350px", iframe: true });
});
//-->
</script>

<div class="forum_container">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td class="forum_header_bolder"><strong>{#Forums_Related#}: </strong> {$topic_title|sanitize}</td>
    </tr>
  </table>
  <table width="100%" cellpadding="0" cellspacing="1" class="forum_tableborder">
    <tr>
      <th colspan="2" class="forum_header "></th>
      <td class="forum_header">{#GlobalTheme#}</td>
      <td width="10" align="center" nowrap="nowrap" class="forum_header ">{#Forums_Header_postreply#}</td>
      <td align="center" nowrap="nowrap" class="forum_header">{#GlobalAutor#}</td>
      <td width="20" align="center" nowrap="nowrap" class="forum_header">{#Forums_Header_hits#}</td>
      <td width="90" align="center" nowrap="nowrap" class="forum_header">{#Rating_Rating#}</td>
      <td width="150" align="center" nowrap="nowrap" class="forum_header">{#Forums_Header_lastpost#}</td>
    </tr>
    {assign var=announce_header value=true}
    {assign var=sticky_header value=true}
    {assign var=default_header value=true}
    {foreach from=$topics item=topic name=topic_loop}
      <tr>
        <td width="10%" class="forum_info_icon">
          {if $topic.opened != 1}
            <img src="{$imgpath}/statusicons/must_moderate.png" alt="{#Forums_ThreadMustUnlock#}" />
          {else}
            {$topic.statusicon}
          {/if}
        </td>
        <td width="10%" align="center" class="forum_info_icon">{posticon icon=$topic.posticon}</td>
        <td class="forum_info_main">
          {if $topic.type == $type_announce && $announce_header} <img  align="right" src="{$imgpath_forums}pinned.png" alt="" /> {#Forums_Announcement#}: &nbsp;
          {elseif $topic.type == $type_sticky && $sticky_header} <img  align="right" src="{$imgpath_forums}pinned.png" alt="" /> {#Forums_Sticky#}: &nbsp;
          {elseif $topic.type == 0 && $default_header}
          {/if}
          <a title="{$topic.title|tooltip}" class="stip forum_links" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$topic.fid}&amp;page=1&amp;t={$topic.title|translit}">{$topic.title|truncate: 80|sanitize}</a>
          {if $topic.viewing>0}
            &nbsp;({$topic.viewing} {#Forums_Topicviewer#})
          {/if}
          {if !empty($topic.attachment)}
            <img class="absmiddle" src="{$imgpath_forums}attachment.gif" alt="" />
          {/if}
          &nbsp;
          {section name=topic_navigation loop=$topic.navigation_page+1 start=1 max=5}
            {if $smarty.section.topic_navigation.first}
              (<img class="absmiddle" src="{$imgpath_forums}pages_forums.png" alt="" /> <a class="forum_links_smaller" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$topic.fid}&amp;page={$smarty.section.topic_navigation.index}&amp;t={$topic.title|translit}">{$smarty.section.topic_navigation.index}</a>
            {elseif $smarty.section.topic_navigation.last}
              <a class="forum_links_smaller" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$topic.fid}&amp;page={$smarty.section.topic_navigation.index}&amp;t={$topic.title|translit}">{$smarty.section.topic_navigation.index}</a>
            {else}
              <a class="forum_links_smaller" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$topic.fid}&amp;page={$smarty.section.topic_navigation.index}&amp;t={$topic.title|translit}">{$smarty.section.topic_navigation.index}</a>
            {/if}
          {/section}
          {if $topic.navigation_page > 5}
            ... <a class="forum_links_smaller" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$topic.fid}&amp;page={$topic.navigation_page}&amp;t={$topic.title|translit}">{#Forums_Link_last_page#}</a> )
          {elseif $topic.navigation_page > 0} )
          {/if}
        </td>
        <td align="center" class="forum_info_meta">
          {if ($topic.replies-1) == 0}
            -
          {else}
            <a class="forum_links_small user_pop"  href="index.php?p=misc&amp;do=showposter&amp;id={$topic.id}">{$topic.replies-1}</a>
          {/if}
        </td>
        <td align="center" class="forum_info_main">
          {if $topic.user_regdate < 2}
            {#Guest#}
          {else}
            <a title="{#Forums_ShowUserProfile#}" class="stip forum_links_small" href="{$topic.autorlink}">{$topic.autor}</a>
          {/if}
        </td>
        <td align="center" class="forum_info_main"> {$topic.views} </td>
        <td align="center" nowrap="nowrap" class="forum_info_main">
          {if $topic.rating}
            <input name="rating_{$topic.id}" type="radio" value="1" class="star" {if $topic.rating == 1}checked="checked"{/if} disabled="disabled" />
            <input name="rating_{$topic.id}" type="radio" value="2" class="star" {if $topic.rating == 2}checked="checked"{/if} disabled="disabled" />
            <input name="rating_{$topic.id}" type="radio" value="3" class="star" {if $topic.rating == 3}checked="checked"{/if} disabled="disabled" />
            <input name="rating_{$topic.id}" type="radio" value="4" class="star" {if $topic.rating == 4}checked="checked"{/if} disabled="disabled" />
            <input name="rating_{$topic.id}" type="radio" value="5" class="star" {if $topic.rating == 5}checked="checked"{/if} disabled="disabled" />
          {else}
            &nbsp;
          {/if}
        </td>
        <td align="right" class="forum_info_main">
          {if $topic.lastposter->datum|date_format: $lang.DateFormatSimple == $smarty.now|date_format: $lang.DateFormatSimple}
            {#today#},&nbsp;{$topic.lastposter->datum|date_format: '%H:%M'}
          {else}
            {$topic.lastposter->datum|date_format: $lang.DateFormat}
          {/if}
          <br />
          {$topic.lastposter->link}
          <a title="{#Forums_GotoLastPost#}" class="stip forum_links_small" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$topic.fid}&amp;page={$topic.next_page}&amp;t={$topic.title|translit}#pid_{$topic.lastposter->id}"><img class="absmiddle" src="{$imgpath_forums}post_latest.png" alt="{#Forums_GotoLastPost#}" /></a>
        </td>
      </tr>
    {/foreach}
  </table>
</div>
{include file="$incpath/forums/forums_footer.tpl"}
