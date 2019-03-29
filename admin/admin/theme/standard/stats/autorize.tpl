<div class="header">{#Stats_Autorize#}</div>
<div class="subheaders">
  {if $admin_settings.Ahelp == 1}
    <a class="colorbox" href="index.php?do=help&amp;sub={$helpquery}&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/s_help.png" alt="" border="0" /> {#GlobalHelp#}</a>&nbsp;&nbsp;&nbsp;
    {/if}
  <a class="colorbox" href="index.php?do=support&amp;sub=send_order&amp;noframes=1"><img class="absmiddle" src="{$imgpath}/send.png" alt="" border="0" /> {#SendOrder#}</a>
</div>
<form method="post" action="index.php?do=stats&amp;sub=autorize">
  <table border="0" cellspacing="3" cellpadding="0">
    <tr>
      <td>{#Search#}: &nbsp;</td>
      <td width="130"><input style="width: 120px" class="input" type="text" name="q" id="q" value="{$smarty.request.q|default:''}" /></td>
      <td>{#DataRecords#}: &nbsp;</td>
      <td width="50"><input class="input" style="width: 30px" type="text" name="pp" id="pp" value="{$limit}" /></td>
      <td><input name="Senden" type="submit" class="button" value="{#Global_search_b#}" />&nbsp;&nbsp;</td>
      <td><input type="button" class="button" onclick="location.href = 'index.php?do=stats&amp;sub=autorize';" value="{#ButtonReset#}" />&nbsp;&nbsp;</td>
        {if perm('settings')}
        <td><input type="button" class="button" onclick="location.href = 'index.php?do=stats&amp;sub=autorizedelall';" value="{#DelAll#}" /></td>
      {/if} </tr>
  </table>
</form>
<table width="100%" border="0" cellspacing="1" cellpadding="5" class="tableborder">
  <tr>
    <td align="center" width="10%" class="headers"><a href="index.php?do=stats&amp;sub=autorize&amp;sort={$idsort|default:'id_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Global_Id#}</a></td>
    <td align="center" width="26%" class="headers"><a href="index.php?do=stats&amp;sub=autorize&amp;sort={$idsort|default:'id_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#User_username#}</a></td>
    <td align="center" width="18%" class="headers"><a href="index.php?do=stats&amp;sub=autorize&amp;sort={$mailsort|default:'mail_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Global_Email#}</a></td>
    <td align="center" width="18%" class="headers"><a href="index.php?do=stats&amp;sub=autorize&amp;sort={$datsort|default:'dat_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Global_Date#}</a></td>
    <td align="center" width="18%" class="headers"><a href="index.php?do=stats&amp;sub=autorize&amp;sort={$ipsort|default:'ip_desc'}&amp;pp={$limit}&amp;page={$smarty.request.page|default:1}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}">{#Stats_IP_Adress#}</a></td>
    <td width="10%" align="center" class="headers">{#Global_Actions#}</td>
  </tr>
  {foreach from=$items item=item}
    <tr class="{cycle values='second,first'}">
      <td align="center"><strong>{$item->Benutzer}</strong></td>
      <td align="center">
        {if perm('users')}
          <a class="colorbox" href="index.php?do=user&amp;sub=edituser&amp;user={$item->Benutzer}&amp;noframes=1">{$item->Name|sanitize}</a>
        {else}
          <strong>{$item->Name|sanitize}</strong>
        {/if}
      </td>
      <td align="center">{$item->Email|sanitize}</td>
      <td align="center" nowrap="nowrap">{$item->Datum|date_format: '%d-%m-%Y, %H:%M:%S'}</td>
      <td align="center"><a class="colorbox" href="http://www.status-x.ru/webtools/whois/{$item->Ip}/">{$item->Ip}</a></td>
      <td align="center" nowrap="nowrap"><a class="stip" title="{$lang.Global_Delete|sanitize}" href="index.php?do=stats&amp;sub=delautorize&amp;id={$item->Id}&amp;pp={$limit}&amp;page={$smarty.request.page}{if !empty($smarty.request.q)}&amp;q={$smarty.request.q|urlencode}{/if}"><img class="absmiddle" src="{$imgpath}/delete.png" alt="" border="0" /></a></td>
    </tr>
  {/foreach}
  {if !empty($navi)}
    <tr>
      <td class="first" colspan="6"> {$navi} </td>
    </tr>
  {/if}
</table>
