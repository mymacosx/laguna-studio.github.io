{script file="$jspath/jrating.js" position='head'}
<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.user_pop').colorbox({ height: "450px", width: "350px", iframe: true });
});
//-->
</script>

{include file="$incpath/forums/user_panel_forums.tpl"}
{include file="$incpath/forums/tree.tpl"}
{if !empty($categories)}
{foreach from=$categories item=categorie}
<script type="text/javascript">
<!-- //
togglePanel('navpanel_cat_{$categorie.id}', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

{assign var=cnx_id value=$categorie.id}
{assign var=cnx value="xtoggles_cat_$cnx_id"}
<div class="round" id="cat_{$categorie.id}">
  <div class="opened" id="navpanel_cat_{$categorie.id}" title="{$categorie.title|sanitize}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;">
    <div class="forum_header_bolder">
      <a class="forum_links_cat" href="{$categorie.link}&amp;t={$categorie.title|translit}">{$categorie.title|sanitize}</a>
      <br />
      {$categorie.comment}
    </div>
    <table width="100%" cellpadding="0" cellspacing="1" class="forum_tableborder">
      {if count($forums[$categorie.title]) > 0}
        <tr>
          <th class="forum_header">&nbsp;</th>
          <td class="forum_header">{#Forums_Title#}</td>
          <td align="center" class="forum_header" nowrap="nowrap">{#Forums_Header_lastpost#}</td>
          <td align="center" class="forum_header" nowrap="nowrap">{#GlobalTheme#}</td>
          <td align="center" class="forum_header" nowrap="nowrap">{#Forums_Header_posts#}</td>
        </tr>
      {/if}
      {foreach from=$forums[$categorie.title] item=i_forum}
        <tr>
          <td width="1%" class="forum_info_icon">{$i_forum.statusicon}</td>
          <td class="forum_info_main">
            <a title="{$i_forum.title|tooltip}" class="stip forum_links" href="{$i_forum.link}&amp;t={$i_forum.title|translit}">{$i_forum.title|sanitize}</a>
            <div class="f_info_comment"> {$i_forum.comment|strip_tags} </div>
            {if count($i_forum.subforums)}
              <div>
                {#Forums_Label_subforums#}: &nbsp;
                {foreach from=$i_forum.subforums item=subforum}
                  <a class="forum_links_small" href="index.php?p=showforum&amp;fid={$subforum->id}">{$subforum->title|sanitize}</a>&nbsp;
                {/foreach}
              </div>
            {/if}
          </td>
          <td class="forum_info_meta" width="15%">
            <div align="right">
              {if !empty($i_forum.lastpost->topic_id)}
                {if $i_forum.lastpost->datum|date_format: $lang.DateFormatSimple == $smarty.now|date_format: $lang.DateFormatSimple}
                  {#today#},&nbsp;{$i_forum.lastpost->datum|date_format: '%H:%M'}
                {else}
                  {$i_forum.lastpost->datum|date_format: $lang.DateFormat}
                {/if}
                <br />
                {if $i_forum.lastpost->regdate < 2}
                  {#Guest#}
                {else}
                  <a title="{#Forums_ShowUserProfile#}" class="stip forum_links_small" href="index.php?p=user&amp;id={$i_forum.lastpost->uid}&amp;area={$area}">{$i_forum.lastpost->uname}</a>
                {/if}
                <a title="{#Forums_GotoLastPost#}" href="index.php?p=showtopic&amp;toid={$i_forum.lastpost->topic_id}&amp;pp=15&amp;page={$i_forum.lastpost->page}#pid_{$i_forum.lastpost->id}" class="stip"><img class="absmiddle" src="{$imgpath_forums}post_latest.png" alt="" /></a>
                <br />
                <a title="{$i_forum.lastpost->title|tooltip}" class="stip forum_links_small" href="index.php?p=showtopic&amp;toid={$i_forum.lastpost->topic_id}&amp;fid={$i_forum.id}&amp;t={$i_forum.lastpost->title|translit}">{$i_forum.lastpost->title|truncate: 25|sanitize}</a>
              {else}
                <div align="center">-</div>
              {/if}
            </div>
          </td>
          <td width="10%" align="center" class="forum_info_meta">
            {if $i_forum.tcount == 0}
              -
            {else}
              {$i_forum.tcount}
            {/if}
          </td>
          <td width="10%" align="center" class="forum_info_meta">
            {if $i_forum.pcount == 0}
              -
            {else}
              {$i_forum.pcount}
            {/if}
          </td>
        </tr>
      {/foreach}
    </table>
  </div>
</div>
{/foreach}
<br />
<br />
{/if}
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="bottom">
      {if $forum.status == 1 && $ugroup == 1}
        <div class="forum_buttons_big"><a href="index.php?p=forums&amp;action=newtopic&amp;fid={$smarty.request.fid}"><img src="{$imgpath_forums}newthread.png" alt="{#Forums_NewTopic#}" />{#Forums_NewTopic#}</a></div>
          {elseif $forum.status == 1}
        <img src="{$imgpath_forums}forum_closed.png" alt="" />
      {else}
        {if $forum.permissions.5}
          <div class="forum_buttons_big"><a href="index.php?p=forums&amp;action=newtopic&amp;fid={$smarty.request.fid}"><img src="{$imgpath_forums}newthread.png" alt="{#Forums_NewTopic#}" />{#Forums_NewTopic#}</a></div>
            {/if}
          {/if}
    </td>
    <td align="right">
      {if $loggedin}
        <a id="forum_menulink" class="forum_links" onclick="toggleContent('forum_menulink', 'forum_menudata');" href="javascript: void(0);">{#Forums_OptionsHere#}</a>
        <div id="forum_menudata" class="status" style="display:none">
          {if $forum.permissions.5}
            <div><a class="menu_link" href="index.php?p=forums&amp;action=newtopic&amp;fid={$smarty.request.fid}"> <img class="absmiddle" src="{$imgpath_forums}more.png" alt="" /> {#Forums_NewTopic#}</a></div>
          {/if}
          {if $loggedin}
            <div><a class="menu_link" href="index.php?p=forum&amp;action=print&amp;what=subscription&amp;id={$forum.id}"> <img class="absmiddle" src="{$imgpath_forums}more.png" alt="" /> {#Forums_ShowAllAbos_thisforum#}</a></div>
            <div><a class="menu_link" href="index.php?p=forum&amp;action=markread&amp;what=forum&amp;id={$forum.id}"> <img class="absmiddle" src="{$imgpath_forums}more.png" alt="" /> {#Forums_MarkTopicAsRead#}</a></div>
          {/if}
        </div>
        &nbsp;|&nbsp;
      {/if}
      <a class="forum_links" href="index.php?p=forum&amp;action=search_mask&amp;fid={$forum.id}">{#Forums_SearchHere#}</a>
      {if !empty($pages)}
        <br />
        <br />
        {$pages}
      {/if}
    </td>
  </tr>
</table>
<br />
{if count($topics)}
  <div class="forum_container">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td class="forum_header_bolder"><strong>{#Forums_TopicsIncluded#}</strong> {$forums_name|escape}</td>
        <td align="right" class="forum_header_bolder">&nbsp;</td>
      </tr>
    </table>
    <table width="100%" cellpadding="0" cellspacing="1" class="forum_tableborder">
      <tr>
        <th colspan="2" class="forum_header "></th>
        <td class="forum_header"><a href="{$sort_by_theme_link}" class="forum_head"> {#GlobalTheme#}</a></td>
        <td width="10" align="center" nowrap="nowrap" class="forum_header "><a href="{$sort_by_reply_link}" class="forum_head"> {#Forums_Header_postreply#} </a></td>
        <td align="center" nowrap="nowrap" class="forum_header"><a href="{$sort_by_author_link}" class="forum_head"> {#GlobalAutor#} </a></td>
        <td width="20" align="center" nowrap="nowrap" class="forum_header"><a href="{$sort_by_hits_link}" class="forum_head"> {#Forums_Header_hits#} </a></td>
        <td width="90" align="center" nowrap="nowrap" class="forum_header"><a href="{$sort_by_rating_link}" class="forum_head"> {#Rating_Rating#}</a></td>
        <td width="150" align="center" nowrap="nowrap" class="forum_header"><a href="{$sort_by_lastpost_link}" class="forum_head">{#Forums_Header_lastpost#}</a></td>
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
            <a title="{$topic.title|tooltip}" class="stip forum_links" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$forum.id}&amp;page=1&amp;t={$topic.title|translit}">{$topic.title|truncate: 80|sanitize}</a>
            {if $topic.viewing>0}
              &nbsp;({$topic.viewing} {#Forums_Topicviewer#})
            {/if}
            {if !empty($topic.attachment)}
              <img class="absmiddle" src="{$imgpath_forums}attachment.gif" alt="" />
            {/if}
            &nbsp;
            {section name=topic_navigation loop=$topic.navigation_page+1 start=1 max=5}
              {if $smarty.section.topic_navigation.first}
                (<img class="absmiddle" src="{$imgpath_forums}pages_forums.png" alt="" /> <a class="forum_links_smaller" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$forum.id}&amp;page={$smarty.section.topic_navigation.index}&amp;t={$topic.title|translit}">{$smarty.section.topic_navigation.index}</a>
              {elseif $smarty.section.topic_navigation.last}
                <a class="forum_links_smaller" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$forum.id}&amp;page={$smarty.section.topic_navigation.index}&amp;t={$topic.title|translit}">{$smarty.section.topic_navigation.index}</a>
              {else}
                <a class="forum_links_smaller" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$forum.id}&amp;page={$smarty.section.topic_navigation.index}&amp;t={$topic.title|translit}">{$smarty.section.topic_navigation.index}</a>
              {/if}
            {/section}
            {if $topic.navigation_page > 5}
              ... <a class="forum_links_smaller" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$forum.id}&amp;page={$topic.navigation_page}&amp;t={$topic.title|translit}">{#Forums_Link_last_page#}</a> )
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
            <a title="{#Forums_GotoLastPost#}" class="stip forum_links_small" href="index.php?p=showtopic&amp;toid={$topic.id}&amp;fid={$forum.id}&amp;page={$topic.next_page}&amp;t={$topic.title|translit}#pid_{$topic.lastposter->id}"><img class="absmiddle" src="{$imgpath_forums}post_latest.png" alt="" /></a>
          </td>
        </tr>
      {/foreach}
      <tr>
        <td colspan="8" class="forum_post_footer"><table width="100%" cellpadding="5">
            <tr>
              <td>
                <form action="index.php?p=showforum&amp;fid={$smarty.request.fid}" method="post">
                  <input type="hidden" name="fid" value="{$smarty.request.fid}" />
                  <select class="input" name="period">
                    <option value="all" {if isset($smarty.request.period) && $smarty.request.period == 'all'}selected{/if}> {#F_all#} </option>
                    <option value="1" {if isset($smarty.request.period) && $smarty.request.period == 1}selected{/if}> {#Forums_OptLast2#} </option>
                    <option value="2" {if isset($smarty.request.period) && $smarty.request.period == 2}selected{/if}> {#Forums_OptLast#} 2 {#Forums_OptDays2#} </option>
                    <option value="5" {if isset($smarty.request.period) && $smarty.request.period == 5}selected{/if}> {#Forums_OptLast#} 5 {#Forums_OptDays#} </option>
                    <option value="10" {if isset($smarty.request.period) && $smarty.request.period == 10}selected{/if}> {#Forums_OptLast#} 10 {#Forums_OptDays#} </option>
                    <option value="20" {if isset($smarty.request.period) && $smarty.request.period == 20}selected{/if}> {#Forums_OptLast#} 20 {#Forums_OptDays#} </option>
                    <option value="30" {if isset($smarty.request.period) && $smarty.request.period == 30}selected{/if}> {#Forums_OptLast#} 30 {#Forums_OptDays#} </option>
                    <option value="40" {if isset($smarty.request.period) && $smarty.request.period == 40}selected{/if}> {#Forums_OptLast#} 40 {#Forums_OptDays#} </option>
                    <option value="50" {if isset($smarty.request.period) && $smarty.request.period == 50}selected{/if}> {#Forums_OptLast#} 50 {#Forums_OptDays#} </option>
                    <option value="100" {if isset($smarty.request.period) && $smarty.request.period == 100}selected{/if}> {#Forums_OptLast#} 100 {#Forums_OptDays#} </option>
                    <option value="365" {if isset($smarty.request.period) && $smarty.request.period == 365}selected{/if}> {#Forums_ShowThisYear#} </option>
                  </select> &nbsp;
                  <select class="input" name="sort">
                    <option value="desc" {if isset($smarty.request.sort) && $smarty.request.sort == 'desc'}selected{/if}> {#Forums_SortDesc#} </option>
                    <option value="asc" {if isset($smarty.request.sort) && $smarty.request.sort == 'asc'}selected{/if}> {#Forums_SortAsc#} </option>
                  </select>&nbsp;
                  <input type="submit" class="button" value="{#GlobalShow#}" />
                </form>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </div>
{/if}
<table width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td valign="bottom">
      {if $forum.permissions.5}
        <div class="forum_buttons_big"><a href="index.php?p=forums&amp;action=newtopic&amp;fid={$smarty.request.fid}"><img src="{$imgpath_forums}newthread.png" alt="{#Forums_NewTopic#}" />{#Forums_NewTopic#}</a></div>
      {elseif $forum.status == 1}
        <img src="{$imgpath_forums}forum_closed.png" alt="" />
      {/if}
    </td>
    <td align="right">
      {if !empty($pages)}
        {$pages}
      {/if}
    </td>
  </tr>
</table>
<br />
<br />
<table width="100%" cellpadding="0" cellspacing="0">
  <tr valign="top">
    <td>
      <table width="450" cellpadding="4" cellspacing="1" class="forum_tableborder">
        <tr>
          <td class="forum_header">{#Forums_ModsHere#}</td>
        </tr>
        <tr>
          <td class="forum_info_main">
            {if $get_mods}
              {$get_mods}
            {else}
              {#Forums_NoModsHere#}
            {/if}
          </td>
        </tr>
      </table>
      <br />
      <table width="450" cellpadding="0" cellspacing="1" class="forum_tableborder">
        <tr>
          <td class="forum_header">{#Forums_IconsDesc#}</td>
        </tr>
        <tr>
          <td class="forum_info_main">
            <img src="{$imgpath}/statusicons/thread_new.png" alt="" />&nbsp;{#NewMessage#}
            <br />
            <img src="{$imgpath}/statusicons/thread.png" alt="" />&nbsp;{#Forums_Label_no_new_post#}
            <br />
            <img src="{$imgpath}/statusicons/thread_hot_new.png" alt="" />&nbsp;{#Forums_Label_hot_post#}
            <br />
            <img src="{$imgpath}/statusicons/thread_hot.png" alt="" />&nbsp;{#Forums_Label_hot_post#}
            <br />
            <img src="{$imgpath}/statusicons/thread_lock_new.png" alt="" />&nbsp;{#Forums_TopicClosed#}
            <br />
            <img src="{$imgpath}/statusicons/thread_lock.png" alt="" />&nbsp;{#Forums_TopicClosed#}
          </td>
        </tr>
      </table>
    </td>
    <td>
      <div align="right">
        <table width="450" cellpadding="4" cellspacing="1" class="forum_tableborder">
          <tr>
            <td class="forum_header">{#Forums_JumpTo#}</td>
          </tr>
          <tr>
            <td nowrap="nowrap" class="forum_info_main">{include file="$incpath/forums/selector.tpl"}</td>
          </tr>
        </table>
        <br />
        <table width="450" cellpadding="0" cellspacing="1" class="forum_tableborder">
          <tr>
            <td class="forum_header">{#Forums_PostRules#}</td>
          </tr>
          <tr>
            <td class="forum_info_main">
              {if $forum.permissions.4 == 1}
                {#Forums_Label_may_download_attachment#}
                <br />
              {else}
                {#Forums_Label_may_not_download_attachment#}
                <br />
              {/if}
              {if $forum.permissions.5 == 1}
                {#Forums_Label_may_post_new_topic#}
                <br />
              {else}
                {#Forums_Label_may_not_post_new_topic#}
                <br />
              {/if}
              {if $forum.permissions.7 == 1}
                {#Forums_Label_may_post_replies#}
                <br />
              {else}
                {#Forums_Label_may_not_post_new_topic#}
                <br />
              {/if}
              {if $forum.permissions.10 == 1}
                {#Forums_Label_may_edit_own_post#}
                <br />
              {else}
                {#Forums_Label_may_not_edit_own_post#}
                <br />
              {/if}
            </td>
          </tr>
        </table>
        <br />
      </div>
    </td>
  </tr>
</table>
{include file="$incpath/forums/forums_footer.tpl"}
