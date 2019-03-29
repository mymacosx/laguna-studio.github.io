{include file="$incpath/forums/user_panel_forums.tpl"}
<div class="box_innerhead"><strong>{#Ignorelist#}</strong></div>
<p> {#Profile_IgnoreList_inf#} </p>
<div class="box_innerhead">{#Profile_IgnoreList_new#}</div>
<div class="infobox">
  <form name="newignore" method="post" action="index.php?sub=add&amp;p=forum&amp;action=ignorelist">
    <p>
      <label><input name="UserName" class="input" type="text" value="{$ignorenew_user_field|default:''}" size="50" maxlength="25" />&nbsp;{#Username#}</label>
    </p>
    <p>
      <label><input name="Reason" class="input" type="text" value="{$ireason|default:''}" size="50" maxlength="75" />&nbsp;{#Profile_IgnoreList_reason#}</label>
    </p>
    <input type="submit" class="button" value="{#Global_Add#}" />
  </form>
</div>
{if $data}
  <div class="box_innerhead">{#Profile_IgnoreList_entries#}</div>
  <form method="post" action="index.php?sub=del_multi&amp;p=forum&amp;action=ignorelist&amp;area={$area}">
    <table width="100%" cellpadding="3" cellspacing="1" class="forum_tableborder">
      <tr>
        <td width="1%" align="center" class="forum_header">&nbsp;</td>
        <td class="forum_header">{#Username#}</td>
        <td align="center" class="forum_header">{#Profile_IgnoreList_reason#}</td>
        <td align="center" class="forum_header">{#Forums_avatar#}</td>
        <td align="center" class="forum_header">{#Forums_onlineStatus#}</td>
      </tr>
      {foreach from=$data item=i}
        <tr class="{cycle name='s' values='row_first,row_second'}">
          <td width="1%" align="center"><input name="del[{$i->IgnorierId}]" type="checkbox" id="del[{$i->IgnorierId}]" value="1" /></td>
          <td>
            <a id="pn_user_{$i->IgnorierId}" onclick="toggleContent('pn_user_{$i->IgnorierId}', 'pn_data_{$i->IgnorierId}');" href="javascript: void(0);"><strong>{$i->Benutzername|sanitize}</strong></a>
            <div class="status" style="display: none" id="pn_data_{$i->IgnorierId}">
              {$i->UserPop}
            </div>
            <br />
            {#Added#} {$i->Datum|date_format: $lang.DateFormat}
          </td>
          <td align="center">{$i->Grund|sanitize}</td>
          <td align="center">{$i->Avatar}</td>
          <td align="center">{onlinestatus uname=$i->Benutzername}</td>
        </tr>
      {/foreach}
    </table>
    <br />
    <input type="hidden" name="sub" value="del_multi" />
    <input type="submit" class="button" value="{#Ignorelist_Del2#}" />
  </form>
{/if}
<br />
