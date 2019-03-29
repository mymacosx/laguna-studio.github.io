<script type="text/javascript">
<!-- //
$(document).ready(function() {
    $('.user_pop').colorbox({ height: "550px", width: "550px", iframe: true });
});
//-->
</script>

{include file="$incpath/forums/user_panel_forums.tpl"}
<div class="forum_header_bolder"><strong>{#Users#}</strong></div>
<form action="index.php?p=members&amp;area={$area}" method="post" name="userlist">
  <table cellpadding="4" cellspacing="1" width="100%" class="forum_tableborder">
    <tr>
      <td width="20%" height="26" class="forum_header">{#Username#}</td>
      <td width="10%" align="center" class="forum_header">{#Forums_avatar#}</td>
      <td width="10%" align="center" class="forum_header">{#RegNew#}</td>
      <td width="5%" align="center" class="forum_header">{#Forums_Postings#}</td>
      <td width="5%" align="center" class="forum_header">{#Forums_User_Website#}</td>
      <td width="5%" align="center" class="forum_header">{#Forums_Pn#}</td>
      <td width="5%" align="center" class="forum_header">{#Forums_SkypeMe#}</td>
      <td width="5%" align="center" class="forum_header">{#Profile_ICQ#}</td>
      <td width="5%" align="center" class="forum_header">{#Email#}</td>
    </tr>
    {if $found == 1}
      {foreach from=$table_data item=user}
        <tr class="{cycle name='ul' values='row_forum_first,row_forum_second'}">
          <td>
            <table width="100%" cellpadding="2" cellspacing="0">
              <tr>
                <td valign="top"><div style="float: right">{onlinestatus uname=$user.name}</div>
                  {if $user.Profil_public == 1}
                    <a class="forum_links" href="{$user.userlink}">{$user.name|sanitize}</a>
                  {else}
                    <strong>{$user.name}</strong>
                  {/if}
                  {if $user.team == 1}
                    <br />
                    {$user.teamName}
                  {else}
                    <br />
                    {$user.rank}
                  {/if}
                </td>
              </tr>
            </table>
          </td>
          <td align="center" nowrap="nowrap">{$user.avatar}</td>
          <td align="center" nowrap="nowrap">{$user.regtime|date_format: $lang.DateFormat|default:'&nbsp;'}</td>
          <td align="center">{$user.posts|default:'&nbsp;'} </td>
          <td align="center">
            {if $user.Webseite}
              <a rel="nofollow" href="{$user.Webseite}" target="_blank"><img src="{$imgpath_forums}home.png" alt="" /></a>
              {/if}
          </td>
          <td align="center">{if $user.Pn_User}{$user.Pn_User}{else}&nbsp;{/if}</td>
          <td align="center">{if $user.Skype_User}{$user.Skype_User}{else}&nbsp;{/if}</td>
          <td align="center">{if $user.Icq_User}{$user.Icq_User}{else}&nbsp;{/if}</td>
          <td align="center">{if $user.Email_User}{$user.Email_User}{else}&nbsp;{/if}</td>
        </tr>
      {/foreach}
    {/if}
    <tr>
      <td colspan="9" class="forum_header">
        <div align="center">
          <table  cellspacing="2" cellpadding="0">
            <tr>
              <td>{#Username#}&nbsp;<input name="suname" type="text" class="input" size="30" value="{$smarty.request.suname|default:''}" maxlength="60" />&nbsp;</td>
              <td>
                <select class="input" name="selby">
                  <option value="joindate" {if isset($sel3)}{$sel3}{/if}>{#show#} {#by_t#} {#Sort_RegDate#}</option>
                  <option value="posts" {if isset($sel2)}{$sel2}{/if}>{#show#} {#by_t#} {#Sort_Postings#}</option>
                  <option value="username" {if isset($sel1)}{$sel1}{/if}>{#show#} {#by_t#} {#Sort_Username#}</option>
                </select>&nbsp;
              </td>
              <td>
                <select  class="input" name="ud">
                  <option value="DESC" {if isset($ud2)}{$ud2}{/if}>{#in_t#} {#Descending#}</option>
                  <option value="ASC" {if isset($ud1)}{$ud1}{/if}>{#in_t#} {#Ascending#}</option>
                </select>&nbsp;
              </td>
              <td><select  class="input" name="pp">{$pp_l}</select>&nbsp;</td>
              <td><input name="Submit" type="submit" class="button" value="{#GlobalShow#}" /></td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>
  <br />
  {if $pagenav}
    {$pagenav}
  {/if}
</form>
{include file="$incpath/forums/forums_footer.tpl"}
