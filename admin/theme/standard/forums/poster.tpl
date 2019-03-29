<div class="forum_header_bolder"><strong>{#Forums_TheseUserReplies#}<strong></div>
<table width="100%" cellpadding="4" cellspacing="1" class="forum_tableborder">
  <tr class="forum_header_bolder">
    <td class="forum_header"><strong>{#Users#}</strong></td>
    <td class="forum_header"><strong>{#Forums_Header_posts#}</strong></td>
  </tr>
  {foreach from=$poster item=post}
    <tr>
      <td class="forum_info_main"><a href="javascript: void(0);" onclick="parent.location.href = 'index.php?p=user&amp;id={$post->uid}&amp;area={$area}';
  closeWindow();">{$post->uname|sanitize}</a></td>
      <td class="forum_info_meta">{$post->ucount}</td>
    </tr>
  {/foreach}
</table>
<br />
<div align="center"><input type="button" class="button" onclick="closeWindow();" value="{#WinClose#}" /></div>
