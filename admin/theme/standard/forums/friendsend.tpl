{include file="$incpath/forums/user_panel_forums.tpl"}
{if !empty($error)}
  <div class="error_box">
    <ul>
      {foreach from=$error item=err}
        <li>{$err}</li>
        {/foreach}
    </ul>
  </div>
{/if}
<form action="" method="post">
  <table width="100%" cellpadding="5" cellspacing="1">
    <tr>
      <td colspan="2" class="forum_header_bolder"><strong>{#FriendSend#}</strong></td>
    </tr>
    <tr class="forum_info_meta">
      <td width="150"><strong>{#GlobalName#}: </strong></td>
      <td><input name="User" style="width: 200px" type="text" value="{if isset($smarty.request.User)}{$smarty.request.User|sanitize}{/if}" /></td>
    </tr>
    <tr class="forum_info_meta">
      <td width="150"><strong>{#Email#}: </strong></td>
      <td><input name="Email" style="width: 200px" type="text" value="{if isset($smarty.request.Email)}{$smarty.request.Email|sanitize}{/if}" /></td>
    </tr>
    <tr class="forum_info_meta">
      <td width="150"><strong>{#GlobalTheme#}: </strong></td>
      <td><input name="Title" style="width: 500px" type="text" value="{if isset($smarty.request.Title)}{$smarty.request.Title|sanitize}{else}{$topic.title|sanitize}{/if}" /></td>
    </tr>
    <tr class="forum_info_meta">
      <td width="150"><strong>{#GlobalMessage#}: </strong></td>
      <td><textarea name="Message" cols="120" rows="10">{if isset($smarty.request.Message)}{$smarty.request.Message|sanitize}{else}{$topic.message|sanitize}{/if}</textarea></td>
    </tr>
    <tr class="forum_info_meta">
      <td width="150"><input type="button" class="button" value="{#Forums_JumpBack#}" onclick="history.go(-1);" /></td>
      <td><input type="submit" class="button" value="{#SendEmail_Send#}" /></td>
    </tr>
  </table>
  <input type="hidden" name="t_id" value="{$smarty.request.t_id}" />
  <input type="hidden" name="send" value="1" />
</form>
{include file="$incpath/forums/forums_footer.tpl"}
