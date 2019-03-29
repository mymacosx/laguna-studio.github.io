{script file="$jspath/jrating.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.user_pop').colorbox({ height: "450px", width: "350px", iframe: true });
});
//-->
</script>

{include file="$incpath/forums/user_panel_forums.tpl"}
<div class="box_innerhead"><strong>{$title_result|sanitize}</strong></div>
<table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
  <tr>
    <td class="forum_header" width="30px">&nbsp;</td>
    <td class="forum_header" width="30px">&nbsp;</td>
    <td class="forum_header">{#GlobalTheme#}</td>
    <td class="forum_header" align="center" width="40px">{#Forums_Header_postreply#}</td>
    <td class="forum_header" align="center">{#GlobalAutor#}</td>
    <td class="forum_header" align="center" width="40px">{#Forums_Header_hits#}</td>
    <td class="forum_header" align="center" width="100px">{#Rating_Rating#}</td>
  </tr>
  {foreach from=$matches item=topic}
    <tr>
      <td class="forum_info_icon">{$topic.statusicon}</td>
      <td class="forum_info_icon">{posticon icon=$topic.posticon}&nbsp;</td>
      <td class="forum_info_main">
        <a class="forum_links" href="{$topic.link}">{$topic.title|sslash}</a> &nbsp;
        {if isset($topic.navigation_count)}
          {section name=topic_navigation loop=$topic.navigation_count+1 start=1 max=7}
            {if $smarty.section.topic_navigation.first}
              (<a href="index.php?p=showtopic&amp;toid={$topic.id}&amp;pp=15&amp;page={$smarty.section.topic_navigation.index}">{$smarty.section.topic_navigation.index}</a>
            {elseif $smarty.section.topic_navigation.last}
              <a href="index.php?p=showtopic&amp;toid={$topic.id}&amp;pp=15&amp;page={$smarty.section.topic_navigation.index}">{$smarty.section.topic_navigation.index}</a>
            {else}
              <a href="index.php?p=showtopic&amp;toid={$topic.id}&amp;pp=15&amp;page={$smarty.section.topic_navigation.index}">{$smarty.section.topic_navigation.index}</a>
            {/if}
          {/section}
          {if $topic.navigation_count > 7}
            ... <a href="index.php?p=showtopic&amp;toid={$topic.id}&amp;pp=15&amp;page={$topic.navigation_count}">{#Forums_Link_last_page#}</a>)
          {elseif $topic.navigation_count > 0})
          {/if}
        {/if}
        <br />
        {#Forums_Title#}: <a href="index.php?p=showforum&amp;fid={$topic.forum_id}&amp;t={$topic.f_title|translit}">{$topic.f_title}</a>
      </td>
      <td class="forum_info_meta" align="center">
        {if $topic.replies-1 == 0}
          0
        {else}
          <a class="user_pop" href="index.php?p=misc&amp;do=showposter&amp;id={$topic.id}">{$topic.replies-1}</a>
        {/if}
      </td>
      <td class="forum_info_main" align="center"><a href="{$topic.autorlink}">{$topic.autor}</a></td>
      <td class="forum_info_meta" align="center">
        {if $topic.views}
          {$topic.views}
        {else}
          &nbsp;
        {/if}
      </td>
      <td class="forum_info_meta" align="center" style="width: 100px">
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
    </tr>
  {/foreach}
</table>
{if !empty($pages)}
  <p>{$pages}</p>
{/if}
<br />
{include file="$incpath/forums/forums_footer.tpl"}
