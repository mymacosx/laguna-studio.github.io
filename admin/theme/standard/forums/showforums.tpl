{include file="$incpath/forums/user_panel_forums.tpl"}
{assign var=fcountperm value=0}
{if !empty($categories)}
{foreach from=$categories item=categorie}

<script type="text/javascript">
<!-- //
togglePanel('navpanel_xcat_{$categorie.id}', 'togglerboxes', 30, '{$basepath}');
//-->
</script>

{assign var=cnx_id value=$categorie.id}
{assign var=cnx value="xtoggles_cat_$cnx_id"}
{if count($forums[$categorie.title]) > 0}
  {assign var=fcountperm value=$fcountperm+1}
  <div class="round" id="cat_{$categorie.id}">
    <div class="opened" id="navpanel_xcat_{$categorie.id}" title="{$categorie.title|sanitize}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;">
      <div class="forum_header_bolder">
        <a title="{$categorie.title|tooltip}" class="stip forum_links_cat" href="{$categorie.link}&amp;t={$categorie.title|translit}">{$categorie.title|sanitize}</a>
        {if $categorie.comment}
          <br />
          {$categorie.comment}
        {/if}
      </div>
      <table width="100%" cellpadding="0" cellspacing="1" class="forum_tableborder">
        {if count($forums[$categorie.title]) > 0}
          <tr>
            <td class="forum_header ">&nbsp;</td>
            <td class="forum_header ">{#Forums_Title#}</td>
            <td align="center" class="forum_header" nowrap="nowrap">{#Forums_Header_lastpost#}</td>
            <td align="center" class="forum_header">{#Forums_Header_default#}</td>
            <td align="center" class="forum_header">{#Forums_Showforums_posts#}</td>
          </tr>
        {/if}
        {foreach from=$forums[$categorie.title] item=forum}
          <tr>
            <td class="forum_info_icon">{$forum.statusicon}</td>
            <td class="forum_info_main">
              <a title="{$forum.title|tooltip}{if $forum.comment} / {$forum.comment|tooltip}{/if}" class="stip forum_links" href="{$forum.link}&amp;t={$forum.title|translit}">{$forum.title|sanitize}</a>
              <div class="f_info_comment">{$forum.comment|sanitize}</div>
              {if count($forum.subforums)}
                <div><span class="f_info_comment"> {#Forums_Label_subforums#}: </span>&nbsp;
                  {foreach from=$forum.subforums item=subforum name="sf"}
                    <a class="forum_links_small" href="{$subforum.link}&amp;t={$subforum.title|translit}">{$subforum.title|sanitize}</a>{if !$smarty.foreach.sf.last},&nbsp;{/if}
                  {/foreach}
                </div>
              {/if}
            </td>
            <td width="15%" nowrap="nowrap" class="forum_info_meta">
              {if isset($forum.last_post->topic_id) && !empty($forum.last_post->topic_id)}
                <div align="right">
                  {if $forum.last_post->datum|date_format: $lang.DateFormatSimple == $smarty.now|date_format: $lang.DateFormatSimple}
                    {#today#},&nbsp;{$forum.last_post->datum|date_format: '%H:%M'}
                  {else}
                    {$forum.last_post->datum|date_format: $lang.DateFormat}
                  {/if}
                  <br />
                  {if $forum.last_post->user_regdate < 2}
                    {#Guest#}
                  {else}
                    <a title="{#Forums_ShowUserProfile#}" href="index.php?p=user&amp;id={$forum.last_post->uid}&amp;area={$area}" class="stip forum_links_small">{$forum.last_post->Benutzername}</a>
                  {/if}
                  <a title="{#Forums_GotoLastPost#}" href="index.php?p=showtopic&amp;toid={$forum.last_post->topic_id}&amp;fid={$forum.id}&amp;page={$forum.last_post->page}&amp;t={$forum.last_post->title|translit}#pid_{$forum.last_post->id}" class="stip"><img src="{$imgpath_forums}post_latest.png" alt="" hspace="2" class="absmiddle" /></a>
                  <br />
                  <a title="{$forum.last_post->title|tooltip}" class="stip forum_links_small" href="index.php?p=showtopic&amp;toid={$forum.last_post->topic_id}&amp;fid={$forum.id}&amp;t={$forum.last_post->title|translit}">{$forum.last_post->title|truncate: 25|sanitize}</a>
                </div>
              {/if}
            </td>
            <td class="forum_info_main" width="10%">
              <div align="center">
                {if $forum.tcount == 0}
                  -
                {else}
                  {$forum.tcount}
                {/if}
              </div>
            </td>
            <td class="forum_info_meta" width="10%">
              <div align="center">
                {if $forum.pcount == 0}
                  -
                {else}
                  {$forum.pcount}
                {/if}
              </div>
            </td>
          </tr>
        {/foreach}
      </table>
    </div>
  </div>
{/if}
{/foreach}
{if !$fcountperm}
  <div class="infobox" style="text-align: center">
    <div class="h4">{#ForumNoForums#}</div>
    {#ForumNoForumsInf#}
  </div>
{/if}
<br style="clear: both" />
<table width="100" align="center" cellpadding="3" cellspacing="0">
  <tr>
    <td nowrap="nowrap" class="forum_info_main"><img src="{$imgpath}/statusicons/forum_new.png" alt="{#Forums_NewPostings#}" hspace="2" class="absmiddle" /> {#Forums_NewPostings#}&nbsp;</td>
    <td nowrap="nowrap" class="forum_info_main"><img src="{$imgpath}/statusicons/forum_old.png" alt="{#Forums_NoNewPostings#}" hspace="2" class="absmiddle" />{#Forums_NoNewPostings#}&nbsp;</td>
    <td nowrap="nowrap" class="forum_info_main"><img src="{$imgpath}/statusicons/forum_old_lock.png" alt="{#Forums_IsClosed#}" hspace="2" class="absmiddle" />{#Forums_IsClosed#}</td>
  </tr>
</table>
{else}
  <strong>{#Forums_EmptyForum#}</strong>
{/if}
{include file="$incpath/forums/forums_footer.tpl"}
