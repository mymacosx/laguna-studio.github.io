{include file="$incpath/forums/user_panel_forums.tpl"}
<div class="box_innerhead"><strong>{$title_result|sanitize}</strong></div>
<table width="100%" cellpadding="5" cellspacing="1" class="forum_tableborder">
  <tr>
    <td class="forum_header">{#Forums_Showforums_posts#}</td>
    <td class="forum_header" align="center" width="100px">{#Date#}</td>
    <td class="forum_header" align="center" width="100px">{#GlobalAutor#}</td>
    <td class="forum_header" align="center" width="100px">{#ThanksPost#}</td>
    <td class="forum_header" align="center" width="70px">{#Forums_Attachments#}</td>
  </tr>
  {foreach from=$matches item=topic}
    <tr>
      <td class="forum_info_main">
        <a class="forum_links" href="{$topic.postlink}">{$topic.ptitle|truncate: 150|sanitize}</a>
        <br />
        {#GlobalTheme#}: <a href="{$topic.link}">{$topic.title|sslash}</a>
        <br />
        {#Forums_Title#}: <a href="index.php?p=showforum&amp;fid={$topic.forum_id}&amp;t={$topic.f_title|translit}">{$topic.f_title|sslash}</a>
      </td>
      <td class="forum_info_meta" align="center" width="100px">{$topic.pdatum}</td>
      <td class="forum_info_main" align="center" width="100px"><a href="{$topic.autorlink}">{$topic.autor}</a></td>
      <td class="forum_info_meta" align="center" width="100px">{$topic.thanks}</td>
      <td class="forum_info_meta" align="center" width="70px">{$topic.attachment}</td>
    </tr>
  {/foreach}
</table>
{if !empty($pages)}
  <p>{$pages}</p>
{/if}
<br />
{include file="$incpath/forums/forums_footer.tpl"}
