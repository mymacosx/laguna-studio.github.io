<a name="friends"></a>
<div class="box_innerhead"><strong>{#Profile_Friends#}</strong></div>
<div class="infobox">
  {#SeenFriend#} {$num} {#FriendsIs#} <a href="index.php?p=user&amp;id={$smarty.request.id}&amp;area={$area}&amp;friends=all#friends">{$num_all} {#Friends#}</a>&nbsp;&nbsp;&nbsp;&nbsp;
  {if $smarty.request.id != $smarty.session.benutzer_id && $smarty.session.benutzer_id != '0'}
    {if $isf == 0}
      {#WaitFriend#}
    {/if}
    {if $isf == 1}
      <a href="index.php?p=user&amp;action=friends&amp;do=add&amp;id={$smarty.request.id}&amp;area={$area}">{#FriendshipCreate#}</a>
    {/if}
  {/if}
  <br />
  <br />
  <table>
    <tr>
      {assign var='n' value=-1}
      {foreach from=$friends item=f}
        {assign var='n' value=$n+1}
        {if $n == $NLine}
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
          {assign var='n' value=0}
        {/if}
        <td width="{$avatar+14}" height="80" valign="bottom" style="text-align: center">
          <a title="{$f.Freundname}" href="index.php?p=user&amp;id={$f.FreundId}&amp;area={$area}">{$f.Avatar}</a>
          <br />
          <a href="index.php?p=user&amp;id={$f.FreundId}&amp;area={$area}">{$f.Freundname}</a>
          <br />
          {if $smarty.request.id == $smarty.session.benutzer_id}
            <small><a onclick="return confirm('{#FriendshipDel_C#}');" href="index.php?p=user&amp;action=friends&amp;do=del&amp;id={$f.FreundId}&amp;area={$area}">{#Delete#}</a></small>
            {/if}
        </td>
      {/foreach}
    </tr>
  </table>
  {if !$friends}
    <small>{#NoFriends#}</small>
  {/if}
  {if $smarty.request.id == $smarty.session.benutzer_id}
    {if $newfriends}
      <br />
      <br />
      <strong>{#AddUsersFriend#}</strong>
      <br />
      <br />
      <table>
        <tr>
          {assign var='nn' value=-1}
          {foreach from=$newfriends item=newf}
            {assign var='nn' value=$nn+1}
            {if $nn == $NLine}
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
              {assign var='nn' value=0}
            {/if}
            <td width="{$avatar+14}" height="80" valign="bottom" style="text-align: center">
              <a title="{$newf.Freundname}" href="index.php?p=user&amp;id={$newf.BenutzerId}">{$newf.Avatar}</a>
              <br />
              <a href="index.php?p=user&amp;id={$newf.BenutzerId}">{$newf.Freundname}</a>
              <br />
              <small><a href="index.php?p=user&amp;action=friends&amp;do=confirm&amp;id={$newf.Id}&amp;area={$area}">{#Gl_Ok#}</a></small>
              <br />
              <small><a href="index.php?p=user&amp;action=friends&amp;do=del&amp;id={$newf.BenutzerId}&amp;area={$area}">{#Gl_No#}</a></small>
            </td>
          {/foreach}
        </tr>
      </table>
    {/if}
  {/if}
</div>
